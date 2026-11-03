<?php

declare(strict_types=1);

namespace DsaToolbox\Examples;

require_once __DIR__ . '/../src/BinarySearch.php';
require_once __DIR__ . '/../src/PriorityQueue.php';
require_once __DIR__ . '/../src/RetryQueue.php';

use DsaToolbox\BinarySearch;
use DsaToolbox\PriorityQueue;
use DsaToolbox\RetryQueue;

/**
 * Enterprise Recharge and Billing Pipeline Example
 *
 * Demonstrates how to build a high-throughput telecom recharge and billing pipeline
 * using dependency-free DSA components:
 *  1. BinarySearch: Fast fee tier & cashback bracket resolution in O(log N).
 *  2. PriorityQueue: Scheduling urgent postpaid bill pay and VIP transactions ahead of standard prepaid top-ups.
 *  3. RetryQueue: Resilient telecom operator dispatch with exponential backoff & jitter.
 */
class RechargeBillingPipeline
{
    /** @var array<int, array{threshold: float, value: array{cashbackPct: float, tierName: string}}> */
    private array $cashbackTiers;

    private PriorityQueue $dispatchQueue;
    private RetryQueue $retryQueue;

    public function __construct()
    {
        // Tier brackets sorted by threshold
        $this->cashbackTiers = [
            ['threshold' => 10.0,  'value' => ['cashbackPct' => 1.0, 'tierName' => 'Bronze Top-Up']],
            ['threshold' => 50.0,  'value' => ['cashbackPct' => 2.5, 'tierName' => 'Silver Top-Up']],
            ['threshold' => 100.0, 'value' => ['cashbackPct' => 4.0, 'tierName' => 'Gold Top-Up']],
            ['threshold' => 500.0, 'value' => ['cashbackPct' => 7.0, 'tierName' => 'Platinum Bill-Pay']],
        ];

        // Max-Heap: higher priority scores processed first
        $this->dispatchQueue = new PriorityQueue(false);

        // Exponential backoff retry engine for telecom gateway timeouts
        $this->retryQueue = new RetryQueue(baseBackoff: 0.5, maxBackoff: 8.0);
    }

    /**
     * Ingests a customer recharge request.
     */
    public function ingestRecharge(string $transactionId, string $customerPhone, float $amount, string $planType): array
    {
        // 1. Resolve cashback bracket via Binary Search in O(log K) time
        $tier = BinarySearch::findTier($this->cashbackTiers, $amount);
        $cashback = $tier ? round(($amount * $tier['cashbackPct']) / 100, 2) : 0.0;
        $tierName = $tier ? $tier['tierName'] : 'Standard Plan';

        // 2. Compute priority score: Postpaid / Utility bills have higher priority than prepaid
        $priorityScore = match ($planType) {
            'EMERGENCY_UTILITY' => 1000 + $amount,
            'POSTPAID_BILL'     => 500 + $amount,
            default             => 100 + $amount, // Standard PREPAID
        };

        $payload = [
            'txId'          => $transactionId,
            'phone'         => $customerPhone,
            'amount'        => $amount,
            'planType'      => $planType,
            'tier'          => $tierName,
            'cashback'      => $cashback,
            'attempts'      => 0
        ];

        // 3. Push into Priority Queue
        $this->dispatchQueue->push($payload, $priorityScore);

        return [
            'status'        => 'QUEUED',
            'txId'          => $transactionId,
            'priorityScore' => $priorityScore,
            'tier'          => $tierName,
            'cashback'      => $cashback,
        ];
    }

    /**
     * Worker loop simulating telecom gateway dispatch with failure retry handling.
     */
    public function processBatch(callable $telecomGatewaySimulator): array
    {
        $processed = [];

        // 1. First process any retry tasks that are due
        $readyRetries = $this->retryQueue->pollReady();
        foreach ($readyRetries as $retryTask) {
            $payload = $retryTask->payload;
            $payload['attempts'] = $retryTask->attempts;
            $success = $telecomGatewaySimulator($payload);
            if ($success) {
                $processed[] = ['txId' => $payload['txId'], 'status' => 'SUCCESS_AFTER_RETRY'];
            } else {
                $this->retryQueue->retry($payload['txId'], 'Telecom upstream gateway 503 error');
                $processed[] = ['txId' => $payload['txId'], 'status' => 'RE_ENQUEUED_BACKOFF'];
            }
        }

        // 2. Dispatch pending items from the Priority Queue
        while (!$this->dispatchQueue->isEmpty()) {
            $item = $this->dispatchQueue->pop();
            $success = $telecomGatewaySimulator($item);

            if ($success) {
                $processed[] = ['txId' => $item['txId'], 'status' => 'SUCCESS', 'amount' => $item['amount']];
            } else {
                // Enqueue to RetryQueue with exponential backoff & full jitter
                $this->retryQueue->enqueue($item['txId'], $item, maxAttempts: 3, delaySeconds: 0.2);
                $processed[] = ['txId' => $item['txId'], 'status' => 'FAILED_SCHEDULED_RETRY'];
            }
        }

        return $processed;
    }
}

// ---------------- Demo Execution ----------------
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo "=== Running Recharge Billing Pipeline Demo ===\n\n";

    $pipeline = new RechargeBillingPipeline();

    // Ingest various recharge requests
    $r1 = $pipeline->ingestRecharge('TX1001', '+15550101', 15.00, 'PREPAID');
    $r2 = $pipeline->ingestRecharge('TX1002', '+15550102', 250.00, 'POSTPAID_BILL');
    $r3 = $pipeline->ingestRecharge('TX1003', '+15550103', 600.00, 'EMERGENCY_UTILITY');

    echo "Ingested: {$r1['txId']} (Tier: {$r1['tier']}, Cashback: \${$r1['cashback']}, Priority: {$r1['priorityScore']})\n";
    echo "Ingested: {$r2['txId']} (Tier: {$r2['tier']}, Cashback: \${$r2['cashback']}, Priority: {$r2['priorityScore']})\n";
    echo "Ingested: {$r3['txId']} (Tier: {$r3['tier']}, Cashback: \${$r3['cashback']}, Priority: {$r3['priorityScore']})\n\n";

    // Simulate gateway execution
    $results = $pipeline->processBatch(function ($item) {
        // High priority utility succeeds, regular prepaid simulates a temporary network timeout
        if ($item['txId'] === 'TX1001') {
            return false; // Simulate transient gateway failure
        }
        return true;
    });

    foreach ($results as $res) {
        echo "Dispatch Result: [{$res['txId']}] -> Status: {$res['status']}\n";
    }

    echo "\nPipeline demonstrated successfully!\n";
}

