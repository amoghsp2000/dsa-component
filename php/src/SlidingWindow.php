<?php

declare(strict_types=1);

namespace DsaToolbox;

use UnderflowException;
use InvalidArgumentException;

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
class SlidingWindow
{
    /** @var array<int, int|float> Monotonic queue maintaining decreasing order for max */
    private array $maxDeque = [];

    /** @var array<int, int|float> Standard FIFO queue */
    private array $queue = [];

    /**
     * Pushes a new value into the sliding window.
     * Amortized O(1) time complexity.
     */
    public function push(int|float $val): void
    {
        $this->queue[] = $val;

        // Evict smaller elements from the back of the monotonic deque
        while (!empty($this->maxDeque) && end($this->maxDeque) < $val) {
            array_pop($this->maxDeque);
        }

        $this->maxDeque[] = $val;
    }

    /**
     * Pops the oldest element from the front of the window.
     *
     * @return int|float Popped element
     * @throws UnderflowException
     */
    public function pop(): int|float
    {
        if (empty($this->queue)) {
            throw new UnderflowException("Sliding window is empty");
        }

        $val = array_shift($this->queue);

        if (!empty($this->maxDeque) && $this->maxDeque[0] === $val) {
            array_shift($this->maxDeque);
        }

        return $val;
    }

    /**
     * Returns the maximum value in the current window in strictly O(1) time.
     *
     * @throws UnderflowException
     */
    public function getMax(): int|float
    {
        if (empty($this->maxDeque)) {
            throw new UnderflowException("Sliding window is empty");
        }
        return $this->maxDeque[0];
    }

    public function count(): int
    {
        return count($this->queue);
    }

    public function isEmpty(): bool
    {
        return empty($this->queue);
    }

    /**
     * Utility method: computes sliding window maximums across an entire array of size N with window size K.
     * Total runtime: strictly O(N) operations.
     *
     * @param array<int, int|float> $nums
     * @param int $k Window size
     * @return array<int, int|float>
     */
    public static function computeSlidingMax(array $nums, int $k): array
    {
        $n = count($nums);
        if ($n === 0 || $k <= 0) {
            return [];
        }
        if ($k > $n) {
            $k = $n;
        }

        $result = [];
        /** @var array<int, int> Stores indices */
        $deque = [];

        for ($i = 0; $i < $n; $i++) {
            // Remove indices falling outside current window [i - k + 1, i]
            while (!empty($deque) && $deque[0] < $i - $k + 1) {
                array_shift($deque);
            }

            // Maintain decreasing monotonic order
            while (!empty($deque) && $nums[end($deque)] <= $nums[$i]) {
                array_pop($deque);
            }

            $deque[] = $i;

            // Record max for window once first k elements are processed
            if ($i >= $k - 1) {
                $result[] = $nums[$deque[0]];
            }
        }

        return $result;
    }
}

