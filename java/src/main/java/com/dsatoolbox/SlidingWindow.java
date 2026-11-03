package com.dsatoolbox;

import java.util.ArrayDeque;
import java.util.ArrayList;
import java.util.Deque;
import java.util.List;
import java.util.NoSuchElementException;

/**
 * Monotonic Queue (Sliding Window Extremes Engine)
 *
 * Traditional DSA Category: Stacks and Queues (Monotonic Deque) + Arrays and Strings
 * Real-World Engineering Use Cases:
 *  - Real-Time Data Processing (streaming metrics, market ticker peaks)
 *  - Logging and Monitoring (sliding 5-minute peak latency or error rate)
 *  - Financial and Payment Systems (sliding drawdown, high-frequency surge detection)
 *  - Performance Optimization (computing window extremes in O(1) amortized vs O(k) linear)
 *
 * Time Complexity:
 *  - push: O(1) amortized
 *  - pop: O(1)
 *  - getMax / getMin: O(1)
 * Space Complexity: O(K) where K is sliding window size
 */
public class SlidingWindow {

    private final Deque<Double> queue = new ArrayDeque<>();
    private final Deque<Double> maxDeque = new ArrayDeque<>();

    public void push(double val) {
        queue.addLast(val);

        while (!maxDeque.isEmpty() && maxDeque.peekLast() < val) {
            maxDeque.pollLast();
        }

        maxDeque.addLast(val);
    }

    public double pop() {
        if (queue.isEmpty()) {
            throw new NoSuchElementException("Sliding window is empty");
        }

        double val = queue.pollFirst();

        if (!maxDeque.isEmpty() && maxDeque.peekFirst() == val) {
            maxDeque.pollFirst();
        }

        return val;
    }

    public double getMax() {
        if (maxDeque.isEmpty()) {
            throw new NoSuchElementException("Sliding window is empty");
        }
        return maxDeque.peekFirst();
    }

    public boolean isEmpty() {
        return queue.isEmpty();
    }

    public int size() {
        return queue.size();
    }

    public static double[] computeSlidingMax(double[] nums, int k) {
        if (nums == null || nums.length == 0 || k <= 0) {
            return new double[0];
        }

        int n = nums.length;
        if (k > n) k = n;

        double[] result = new double[n - k + 1];
        Deque<Integer> deque = new ArrayDeque<>();

        for (int i = 0; i < n; i++) {
            while (!deque.isEmpty() && deque.peekFirst() < i - k + 1) {
                deque.pollFirst();
            }

            while (!deque.isEmpty() && nums[deque.peekLast()] <= nums[i]) {
                deque.pollLast();
            }

            deque.addLast(i);

            if (i >= k - 1) {
                result[i - k + 1] = nums[deque.peekFirst()];
            }
        }

        return result;
    }
}

