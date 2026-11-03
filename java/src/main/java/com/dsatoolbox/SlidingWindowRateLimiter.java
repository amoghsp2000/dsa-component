package com.dsatoolbox;

import java.util.ArrayDeque;
import java.util.Deque;
import java.util.HashMap;
import java.util.Map;

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
public class SlidingWindowRateLimiter {

    private final int maxRequests;
    private final double windowSeconds;
    private final Map<String, Deque<Double>> requestLogs = new HashMap<>();

    public SlidingWindowRateLimiter(int maxRequests, double windowSeconds) {
        if (maxRequests < 1) {
            throw new IllegalArgumentException("maxRequests must be at least 1");
        }
        if (windowSeconds <= 0) {
            throw new IllegalArgumentException("windowSeconds must be greater than 0");
        }
        this.maxRequests = maxRequests;
        this.windowSeconds = windowSeconds;
    }

    public synchronized boolean allow(String key) {
        return allow(key, System.currentTimeMillis() / 1000.0);
    }

    public synchronized boolean allow(String key, double now) {
        double windowStart = now - windowSeconds;
        cleanupKey(key, windowStart);

        Deque<Double> log = requestLogs.computeIfAbsent(key, k -> new ArrayDeque<>());
        if (log.size() < maxRequests) {
            log.addLast(now);
            return true;
        }

        return false;
    }

    public synchronized int getRemaining(String key) {
        return getRemaining(key, System.currentTimeMillis() / 1000.0);
    }

    public synchronized int getRemaining(String key, double now) {
        double windowStart = now - windowSeconds;
        cleanupKey(key, windowStart);

        Deque<Double> log = requestLogs.get(key);
        int count = log != null ? log.size() : 0;
        return Math.max(0, maxRequests - count);
    }

    public synchronized double getResetTime(String key) {
        return getResetTime(key, System.currentTimeMillis() / 1000.0);
    }

    public synchronized double getResetTime(String key, double now) {
        double windowStart = now - windowSeconds;
        cleanupKey(key, windowStart);

        Deque<Double> log = requestLogs.get(key);
        if (log == null || log.size() < maxRequests) {
            return now;
        }

        double oldest = log.peekFirst();
        return oldest + windowSeconds;
    }

    public synchronized void reset(String key) {
        requestLogs.remove(key);
    }

    private void cleanupKey(String key, double windowStart) {
        Deque<Double> log = requestLogs.get(key);
        if (log == null) return;

        while (!log.isEmpty() && log.peekFirst() <= windowStart) {
            log.pollFirst();
        }
    }
}

