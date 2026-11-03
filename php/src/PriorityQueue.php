<?php

declare(strict_types=1);

namespace DsaToolbox;

use UnderflowException;

/**
 * Priority Queue (Min / Max Binary Heap)
 *
 * Traditional DSA Category: Trees and Graphs
 * Real-World Engineering Use Cases:
 *  - Queue and Background Job Processing (urgent tasks & VIP transactions)
 *  - Scheduling and Task Management (earliest scheduled execution timestamp)
 *  - Real-Time Data Processing (top-K elements, stream prioritization)
 *
 * Time Complexity:
 *  - push: O(log N)
 *  - pop: O(log N)
 *  - peek: O(1)
 *  - count: O(1)
 * Space Complexity: O(N)
 *
 * @template T
 */
class PriorityQueue
{
    /** @var array<int, array{value: mixed, priority: int|float}> */
    private array $heap = [];

    /** @var bool True for min-heap (lowest numerical priority first), false for max-heap */
    private bool $isMinHeap;

    /**
     * @param bool $isMinHeap When true, lower numbers have higher priority (e.g. earliest timestamp).
     *                        When false, higher numbers have higher priority (e.g. VIP billing score).
     */
    public function __construct(bool $isMinHeap = true)
    {
        $this->isMinHeap = $isMinHeap;
    }

    /**
     * Pushes a value with an associated numerical priority.
     *
     * @param mixed $value Payload
     * @param int|float $priority Numerical priority metric
     */
    public function push(mixed $value, int|float $priority): void
    {
        $this->heap[] = [
            'value' => $value,
            'priority' => $priority
        ];
        $this->bubbleUp(count($this->heap) - 1);
    }

    /**
     * Extracts and returns the highest-priority element.
     *
     * @return mixed Payload of the extracted element
     * @throws UnderflowException If the queue is empty
     */
    public function pop(): mixed
    {
        if (empty($this->heap)) {
            throw new UnderflowException("Cannot pop from an empty PriorityQueue");
        }

        $root = $this->heap[0]['value'];
        $last = array_pop($this->heap);

        if (!empty($this->heap)) {
            $this->heap[0] = $last;
            $this->siftDown(0);
        }

        return $root;
    }

    /**
     * Returns the highest-priority element without removing it.
     *
     * @return mixed
     * @throws UnderflowException If the queue is empty
     */
    public function peek(): mixed
    {
        if (empty($this->heap)) {
            throw new UnderflowException("Cannot peek into an empty PriorityQueue");
        }
        return $this->heap[0]['value'];
    }

    /**
     * Returns the highest priority value (numerical priority) without removing it.
     *
     * @return int|float
     */
    public function peekPriority(): int|float
    {
        if (empty($this->heap)) {
            throw new UnderflowException("Cannot peek priority of an empty PriorityQueue");
        }
        return $this->heap[0]['priority'];
    }

    public function count(): int
    {
        return count($this->heap);
    }

    public function isEmpty(): bool
    {
        return empty($this->heap);
    }

    public function clear(): void
    {
        $this->heap = [];
    }

    /**
     * Compares priority of item A vs item B according to min-heap or max-heap policy.
     * Returns true if $a has higher precedence than $b.
     */
    private function hasPrecedence(int|float $p1, int|float $p2): bool
    {
        return $this->isMinHeap ? ($p1 < $p2) : ($p1 > $p2);
    }

    private function bubbleUp(int $index): void
    {
        while ($index > 0) {
            $parentIndex = intdiv($index - 1, 2);
            if ($this->hasPrecedence($this->heap[$index]['priority'], $this->heap[$parentIndex]['priority'])) {
                $temp = $this->heap[$index];
                $this->heap[$index] = $this->heap[$parentIndex];
                $this->heap[$parentIndex] = $temp;
                $index = $parentIndex;
            } else {
                break;
            }
        }
    }

    private function siftDown(int $index): void
    {
        $size = count($this->heap);
        while (true) {
            $best = $index;
            $left = 2 * $index + 1;
            $right = 2 * $index + 2;

            if ($left < $size && $this->hasPrecedence($this->heap[$left]['priority'], $this->heap[$best]['priority'])) {
                $best = $left;
            }
            if ($right < $size && $this->hasPrecedence($this->heap[$right]['priority'], $this->heap[$best]['priority'])) {
                $best = $right;
            }

            if ($best !== $index) {
                $temp = $this->heap[$index];
                $this->heap[$index] = $this->heap[$best];
                $this->heap[$best] = $temp;
                $index = $best;
            } else {
                break;
            }
        }
    }
}

