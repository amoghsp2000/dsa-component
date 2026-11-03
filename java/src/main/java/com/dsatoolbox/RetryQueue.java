package com.dsatoolbox;

import java.util.*;

/**
 * Task item within RetryQueue.
 *
 * @param <T> Payload type
 */
class RetryTask<T> {
    public final String id;
    public final T payload;
    public int attempts = 0;
    public final int maxAttempts;
    public double nextExecutionTime;
    public String lastError = null;

    public RetryTask(String id, T payload, int maxAttempts, double nextExecutionTime) {
        this.id = id;
        this.payload = payload;
        this.maxAttempts = maxAttempts;
        this.nextExecutionTime = nextExecutionTime;
    }
}

/**
 * Retry Queue with Exponential Backoff & Full Jitter
 *
 * Traditional DSA Category: Stacks and Queues (Priority Min-Heap) + Greedy Algorithms
 * Real-World Engineering Use Cases:
 *  - Queue and Background Job Processing (resilient task retries, worker orchestration)
 *  - Financial and Payment Systems (payment gateway webhooks, recharge retries)
 *  - API and HTTP Processing (mitigating thundering herd during third-party outages)
 *
 * Backoff Formula (Full Jitter):
 *   sleep = random_float(0, min(maxBackoff, baseBackoff * (2 ^ attempt)))
 *
 * Time Complexity:
 *  - enqueue: O(log N)
 *  - pollReady: O(log N) per ready item, O(1) peek when none ready
 *  - retry: O(log N)
 * Space Complexity: O(N)
 *
 * @param <T> Payload type
 */
public class RetryQueue<T> {

    private final double baseBackoff;
    private final double maxBackoff;
    private final Random random = new Random();

    private final List<Map.Entry<Double, RetryTask<T>>> heap = new ArrayList<>();
    private final Map<String, RetryTask<T>> taskIndex = new HashMap<>();
    private final List<RetryTask<T>> deadLetterQueue = new ArrayList<>();

    public RetryQueue(double baseBackoff, double maxBackoff) {
        if (baseBackoff <= 0 || maxBackoff < baseBackoff) {
            throw new IllegalArgumentException("Invalid backoff configuration: base must be > 0 and <= max");
        }
        this.baseBackoff = baseBackoff;
        this.maxBackoff = maxBackoff;
    }

    public synchronized void enqueue(String id, T payload, int maxAttempts, double delaySeconds) {
        double now = System.currentTimeMillis() / 1000.0;
        double due = now + Math.max(0.0, delaySeconds);

        RetryTask<T> task = new RetryTask<>(id, payload, maxAttempts, due);
        taskIndex.put(id, task);
        heapPush(task, due);
    }

    public synchronized boolean retry(String id, String reason) {
        RetryTask<T> task = taskIndex.get(id);
        if (task == null) return false;

        task.attempts++;
        task.lastError = reason;

        if (task.attempts >= task.maxAttempts) {
            deadLetterQueue.add(task);
            taskIndex.remove(id);
            return false;
        }

        double multiplier = Math.pow(2.0, task.attempts);
        double calculatedBackoff = Math.min(maxBackoff, baseBackoff * multiplier);
        double randomDelay = random.nextDouble() * calculatedBackoff;

        task.nextExecutionTime = (System.currentTimeMillis() / 1000.0) + randomDelay;
        heapPush(task, task.nextExecutionTime);
        return true;
    }

    public synchronized List<RetryTask<T>> pollReady() {
        return pollReady(System.currentTimeMillis() / 1000.0);
    }

    public synchronized List<RetryTask<T>> pollReady(double now) {
        List<RetryTask<T>> ready = new ArrayList<>();

        while (!heap.isEmpty()) {
            double nextDue = heap.get(0).getKey();
            if (nextDue > now) {
                break;
            }

            RetryTask<T> task = heapPop();
            if (task != null && taskIndex.containsKey(task.id)) {
                ready.add(task);
            }
        }

        return ready;
    }

    public synchronized Double peekNextDueTime() {
        return heap.isEmpty() ? null : heap.get(0).getKey();
    }

    public synchronized List<RetryTask<T>> getDeadLetterQueue() {
        return new ArrayList<>(deadLetterQueue);
    }

    public synchronized int size() {
        return taskIndex.size();
    }

    public synchronized int dlqSize() {
        return deadLetterQueue.size();
    }

    private void heapPush(RetryTask<T> task, double priority) {
        heap.add(new AbstractMap.SimpleEntry<>(priority, task));
        int idx = heap.size() - 1;

        while (idx > 0) {
            int parent = (idx - 1) / 2;
            if (heap.get(idx).getKey() < heap.get(parent).getKey()) {
                Map.Entry<Double, RetryTask<T>> tmp = heap.get(idx);
                heap.set(idx, heap.get(parent));
                heap.set(parent, tmp);
                idx = parent;
            } else {
                break;
            }
        }
    }

    private RetryTask<T> heapPop() {
        if (heap.isEmpty()) return null;
        RetryTask<T> root = heap.get(0).getValue();
        Map.Entry<Double, RetryTask<T>> last = heap.remove(heap.size() - 1);

        if (!heap.isEmpty()) {
            heap.set(0, last);
            siftDown(0);
        }

        return root;
    }

    private void siftDown(int idx) {
        int size = heap.size();
        while (true) {
            int smallest = idx;
            int left = 2 * idx + 1;
            int right = 2 * idx + 2;

            if (left < size && heap.get(left).getKey() < heap.get(smallest).getKey()) {
                smallest = left;
            }
            if (right < size && heap.get(right).getKey() < heap.get(smallest).getKey()) {
                smallest = right;
            }

            if (smallest != idx) {
                Map.Entry<Double, RetryTask<T>> tmp = heap.get(idx);
                heap.set(idx, heap.get(smallest));
                heap.set(smallest, tmp);
                idx = smallest;
            } else {
                break;
            }
        }
    }
}

