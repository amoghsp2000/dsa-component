<?php

declare(strict_types=1);

namespace DsaToolbox;

use InvalidArgumentException;

/**
 * Sliding Window Rate Limiter
 *
 * Traditional DSA Category: Stacks and Queues (Sliding Window Deque)
 * Real-World Engineering Use Cases:
 *  - Rate Limiting and Throttling (API quota enforcement, anti-scraping)
 *  - API and HTTP Processing (protecting upstream services against surges)
 *  - Financial and Payment Systems (preventing rapid double-spending or card stuffing)
 *
 * Time Complexity:
 *  - allow: O(K) amortized, where K is expired timestamps in the window
 *  - getRemaining: O(1)
 *  - getResetTime: O(1)
 * Space Complexity: O(U * M) where U is active users and M is max requests per window
 */
class SlidingWindowRateLimiter
{
    private int $maxRequests;
    private float $windowSeconds;

    /** @var array<string, array<int, float>> Map of key => array of microsecond timestamps */
    private array $requestLogs = [];

    /**
     * @param int $maxRequests Maximum number of requests allowed in the window
     * @param float $windowSeconds Length of the sliding time window in seconds
     * @throws InvalidArgumentException
     */
    public function __construct(int $maxRequests, float $windowSeconds)
    {
        if ($maxRequests < 1) {
            throw new InvalidArgumentException("maxRequests must be at least 1");
        }
        if ($windowSeconds <= 0.0) {
            throw new InvalidArgumentException("windowSeconds must be greater than 0");
        }

        $this->maxRequests = $maxRequests;
        $this->windowSeconds = $windowSeconds;
    }

    /**
     * Attempts to consume a token for the given key.
     * Returns true if request is permitted, false if rate limited.
     *
     * @param string $key Identifier (e.g. client IP, user ID, API key)
     * @param float|null $now Current timestamp in float seconds (defaults to microtime(true))
     * @return bool
     */
    public function allow(string $key, ?float $now = null): bool
    {
        $now = $now ?? microtime(true);
        $windowStart = $now - $this->windowSeconds;

        $this->cleanupKey($key, $windowStart);

        if (!isset($this->requestLogs[$key])) {
            $this->requestLogs[$key] = [];
        }

        if (count($this->requestLogs[$key]) < $this->maxRequests) {
            $this->requestLogs[$key][] = $now;
            return true;
        }

        return false;
    }

    /**
     * Returns the remaining number of permitted requests in the current window.
     */
    public function getRemaining(string $key, ?float $now = null): int
    {
        $now = $now ?? microtime(true);
        $windowStart = $now - $this->windowSeconds;

        $this->cleanupKey($key, $windowStart);

        $currentCount = isset($this->requestLogs[$key]) ? count($this->requestLogs[$key]) : 0;
        return max(0, $this->maxRequests - $currentCount);
    }

    /**
     * Calculates the timestamp (in float seconds) when the next request token will become available.
     * Returns $now if tokens are currently available.
     */
    public function getResetTime(string $key, ?float $now = null): float
    {
        $now = $now ?? microtime(true);
        $windowStart = $now - $this->windowSeconds;

        $this->cleanupKey($key, $windowStart);

        if (!isset($this->requestLogs[$key]) || count($this->requestLogs[$key]) < $this->maxRequests) {
            return $now;
        }

        // The earliest recorded timestamp will expire windowSeconds after its creation
        $oldestTimestamp = $this->requestLogs[$key][0];
        return $oldestTimestamp + $this->windowSeconds;
    }

    /**
     * Resets rate limit records for a given key.
     */
    public function reset(string $key): void
    {
        unset($this->requestLogs[$key]);
    }

    /**
     * Garbage-collects all stale keys whose logs have completely expired.
     */
    public function prune(?float $now = null): void
    {
        $now = $now ?? microtime(true);
        $windowStart = $now - $this->windowSeconds;

        foreach (array_keys($this->requestLogs) as $key) {
            $this->cleanupKey($key, $windowStart);
            if (empty($this->requestLogs[$key])) {
                unset($this->requestLogs[$key]);
            }
        }
    }

    private function cleanupKey(string $key, float $windowStart): void
    {
        if (!isset($this->requestLogs[$key])) {
            return;
        }

        // Shift out timestamps older than sliding windowStart
        while (!empty($this->requestLogs[$key]) && $this->requestLogs[$key][0] <= $windowStart) {
            array_shift($this->requestLogs[$key]);
        }
    }
}

