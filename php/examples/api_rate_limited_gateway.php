<?php

declare(strict_types=1);

namespace DsaToolbox\Examples;

require_once __DIR__ . '/../src/Trie.php';
require_once __DIR__ . '/../src/SlidingWindowRateLimiter.php';

use DsaToolbox\Trie;
use DsaToolbox\SlidingWindowRateLimiter;

/**
 * Enterprise API Rate-Limited Gateway Example
 *
 * Demonstrates:
 *  1. Trie: Prefix-based URL route matcher and handler dispatcher.
 *  2. SlidingWindowRateLimiter: Precision sliding window rate limiter enforcing per-client quotas.
 */
class ApiGateway
{
    private Trie $router;
    private SlidingWindowRateLimiter $rateLimiter;

    public function __construct(int $maxRequestsPerWindow = 5, float $windowSeconds = 10.0)
    {
        $this->router = new Trie();
        $this->rateLimiter = new SlidingWindowRateLimiter($maxRequestsPerWindow, $windowSeconds);

        // Register API endpoints into Prefix Trie
        $this->router->insert('/api/v1/recharge/prepaid', ['handler' => 'RechargeController::prepaid', 'auth' => true]);
        $this->router->insert('/api/v1/recharge/postpaid', ['handler' => 'RechargeController::postpaid', 'auth' => true]);
        $this->router->insert('/api/v1/billing/invoice', ['handler' => 'BillingController::getInvoice', 'auth' => true]);
        $this->router->insert('/api/v1/auth/token', ['handler' => 'AuthController::issueToken', 'auth' => false]);
    }

    /**
     * Handles an incoming HTTP request.
     */
    public function handleRequest(string $clientIp, string $path): array
    {
        // 1. Sliding Window Rate Limit Check
        $allowed = $this->rateLimiter->allow($clientIp);
        $remaining = $this->rateLimiter->getRemaining($clientIp);

        if (!$allowed) {
            $resetTime = $this->rateLimiter->getResetTime($clientIp);
            $retryAfter = max(1, (int) ceil($resetTime - microtime(true)));
            return [
                'statusCode' => 429,
                'headers' => [
                    'X-RateLimit-Remaining' => 0,
                    'Retry-After' => $retryAfter,
                ],
                'body' => ['error' => 'Too Many Requests', 'retry_after_seconds' => $retryAfter]
            ];
        }

        // 2. Fast Route Matching using Trie
        $route = $this->router->getValue($path);
        if ($route === null) {
            return [
                'statusCode' => 404,
                'headers' => ['X-RateLimit-Remaining' => $remaining],
                'body' => ['error' => 'Route Not Found']
            ];
        }

        // 3. Dispatch to handler
        return [
            'statusCode' => 200,
            'headers' => ['X-RateLimit-Remaining' => $remaining],
            'body' => [
                'status'  => 'OK',
                'path'    => $path,
                'handler' => $route['handler'],
                'auth'    => $route['auth']
            ]
        ];
    }
}

// ---------------- Demo Execution ----------------
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo "=== Running API Rate Limited Gateway Demo ===\n\n";

    $gateway = new ApiGateway(maxRequestsPerWindow: 3, windowSeconds: 5.0);
    $client = '192.168.1.100';

    // Simulate 4 requests in rapid succession
    for ($i = 1; $i <= 4; $i++) {
        $response = $gateway->handleRequest($client, '/api/v1/recharge/prepaid');
        $rem = $response['headers']['X-RateLimit-Remaining'];
        echo "Request #$i -> Status: {$response['statusCode']} (Remaining: $rem)\n";
    }

    // Test route not found
    $notFound = $gateway->handleRequest('10.0.0.1', '/api/v1/unknown');
    echo "\nRoute Lookup test: {$notFound['statusCode']} - " . json_encode($notFound['body']) . "\n";

    echo "\nGateway demo finished!\n";
}

