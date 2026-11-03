<?php

declare(strict_types=1);

namespace DsaToolbox\Examples;

require_once __DIR__ . '/../src/BloomFilter.php';
require_once __DIR__ . '/../src/UnionFind.php';
require_once __DIR__ . '/../src/PrefixSum.php';

use DsaToolbox\BloomFilter;
use DsaToolbox\UnionFind;
use DsaToolbox\PrefixSum;

/**
 * Enterprise Payment Deduplication and Fraud Detection Engine
 *
 * Demonstrates:
 *  1. BloomFilter: Ultra-fast pre-screening for idempotent transaction keys.
 *  2. UnionFind: Detects circular transaction networks (fraud rings) in O(alpha(N)) time.
 *  3. PrefixSum: Instant O(1) range queries for user balance ledger auditing.
 */
class PaymentDeduplicationEngine
{
    private BloomFilter $idempotencyFilter;
    private UnionFind $fraudRingDetector;

    /** @var array<string, bool> Authoritative DB persistence simulation */
    private array $databaseLedger = [];

    /** @var array<int, float> Sequence of transaction amounts for ledger auditing */
    private array $transactionHistory = [];
    private ?PrefixSum $prefixSumEngine = null;

    public function __construct()
    {
        // 100,000 expected keys with 0.1% false positive rate
        $this->idempotencyFilter = new BloomFilter(100000, 0.001);
        $this->fraudRingDetector = new UnionFind();
    }

    /**
     * Processes a payment transaction with duplicate protection and fraud ring checks.
     */
    public function processPayment(string $idempotencyKey, string $fromAccount, string $toAccount, float $amount): array
    {
        // 1. Bloom Filter fast pre-check: If not in filter, definitively never seen!
        if ($this->idempotencyFilter->contains($idempotencyKey)) {
            // Might be duplicate; verify against authoritative database
            if (isset($this->databaseLedger[$idempotencyKey])) {
                return [
                    'status' => 'REJECTED_DUPLICATE',
                    'reason' => 'Idempotency key already committed'
                ];
            }
        }

        // 2. Fraud Ring Check using Union-Find: Detect if transaction links two accounts in existing cluster
        $createsCycle = !$this->fraudRingDetector->union($fromAccount, $toAccount);
        $clusterSize = $this->fraudRingDetector->getComponentSize($fromAccount);

        if ($createsCycle && $clusterSize >= 3) {
            // Circular dependency detected among multiple transacting parties
            return [
                'status'  => 'FLAGGED_FRAUD_RING',
                'reason'  => "Circular transaction detected between $fromAccount and $toAccount",
                'cluster' => $clusterSize
            ];
        }

        // 3. Commit transaction
        $this->idempotencyFilter->add($idempotencyKey);
        $this->databaseLedger[$idempotencyKey] = true;
        $this->transactionHistory[] = $amount;
        $this->prefixSumEngine = null; // Invalidate cached prefix sums

        return [
            'status'     => 'APPROVED',
            'key'        => $idempotencyKey,
            'amount'     => $amount,
            'clusterSize'=> $clusterSize
        ];
    }

    /**
     * Audits total transaction revenue between transaction indices [$startIdx, $endIdx] in O(1) time.
     */
    public function auditRevenueRange(int $startIdx, int $endIdx): float
    {
        if ($this->prefixSumEngine === null) {
            $this->prefixSumEngine = new PrefixSum($this->transactionHistory);
        }
        return (float) $this->prefixSumEngine->queryRange($startIdx, $endIdx);
    }
}

// ---------------- Demo Execution ----------------
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo "=== Running Payment Deduplication & Fraud Detection Demo ===\n\n";

    $engine = new PaymentDeduplicationEngine();

    // 1. Process normal payments
    $p1 = $engine->processPayment("tx_tok_99182", "USER_ALICE", "USER_BOB", 150.00);
    $p2 = $engine->processPayment("tx_tok_99183", "USER_BOB", "USER_CHARLIE", 120.00);
    echo "P1: {$p1['status']} (\${$p1['amount']})\n";
    echo "P2: {$p2['status']} (\${$p2['amount']})\n";

    // 2. Test duplicate idempotency key
    $dup = $engine->processPayment("tx_tok_99182", "USER_ALICE", "USER_BOB", 150.00);
    echo "Duplicate Check: {$dup['status']} - {$dup['reason']}\n";

    // 3. Test circular transaction fraud ring: Charlie -> Alice (closing Alice -> Bob -> Charlie -> Alice cycle)
    $fraud = $engine->processPayment("tx_tok_99184", "USER_CHARLIE", "USER_ALICE", 100.00);
    echo "Cycle Check: {$fraud['status']} - {$fraud['reason']} (Accounts involved: {$fraud['cluster']})\n";

    // 4. Test ledger range audit using PrefixSum
    $engine->processPayment("tx_tok_99185", "USER_DAVE", "USER_EVE", 300.00);
    $audit = $engine->auditRevenueRange(0, 2);
    echo "Audited Total for First 3 Approved Transactions: \${$audit}\n";

    echo "\nPayment Engine demo completed!\n";
}

