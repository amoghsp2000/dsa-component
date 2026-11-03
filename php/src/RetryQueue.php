<?php

declare(strict_types=1);

namespace DsaToolbox;

use InvalidArgumentException;

/**
 * Task item within RetryQueue.
 */
class RetryTask
{
    public string $id;
    public mixed $payload;
    public int $attempts = 0;
    public int $maxAttempts;
    public float $nextExecutionTime;
    public ?string $lastError = null;

    public function __construct(string $id, mixed $payload, int $maxAttempts, float $nextExecutionTime)
    {
        $this->id = $id;
        $this->payload = $payload;
        $this->maxAttempts = $maxAttempts;
        $this->nextExecutionTime = $nextExecutionTime;
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
 */
class RetryQueue
{
    private float $baseBackoff;
    private float $maxBackoff;

    /** @var array<int, array{priority: float, task: RetryTask}> Internal min-heap */
    private array $heap = [];

    /** @var array<string, RetryTask> Active tasks by ID */
    private array $taskIndex = [];

    /** @var array<int, RetryTask> Dead Letter Queue (DLQ) for permanently failed tasks */
    private array $deadLetterQueue = [];

    /**
     * @param float $baseBackoff Initial backoff delay in seconds (e.g. 1.0)
     * @param float $maxBackoff Upper bound cap for retry delays in seconds (e.g. 60.0)
     * @throws InvalidArgumentException
     */
    public function __construct(float $baseBackoff = 1.0, float $maxBackoff = 60.0)
    {
        if ($baseBackoff <= 0.0 || $maxBackoff < $baseBackoff) {
            throw new InvalidArgumentException("Invalid backoff configuration: base must be > 0 and <= max");
        }
        $this->baseBackoff = $baseBackoff;
        $this->maxBackoff = $maxBackoff;
    }

    /**
     * Enqueues a new task for immediate or delayed execution.
     *
     * @param string $id Unique task identifier
     * @param mixed $payload Task payload
     * @param int $maxAttempts Maximum retry attempts before routing to Dead Letter Queue
     * @param float|null $delaySeconds Initial delay in seconds (0 for immediate)
     */
    public function enqueue(string $id, mixed $payload, int $maxAttempts = 5, ?float $delaySeconds = 0.0): void
    {
        $now = microtime(true);
        $due = $now + max(0.0, (float) $delaySeconds);

        $task = new RetryTask($id, $payload, $maxAttempts, $due);
        $this->taskIndex[$id] = $task;
        $this->heapPush($task, $due);
    }

    /**
     * Re-schedules a failed task with exponential backoff and randomized full jitter.
     * If the task has reached maxAttempts, it is automatically routed to the Dead Letter Queue.
     *
     * @param string $id Task ID
     * @param string|null $reason Error message or failure description
     * @return bool True if re-enqueued, false if routed to DLQ
     */
    public function retry(string $id, ?string $reason = null): bool
    {
        if (!isset($this->taskIndex[$id])) {
            return false;
        }

        $task = $this->taskIndex[$id];
        $task->attempts++;
        $task->lastError = $reason;

        if ($task->attempts >= $task->maxAttempts) {
            $this->deadLetterQueue[] = $task;
            unset($this->taskIndex[$id]);
            return false;
        }

        // Full Jitter calculation
        $multiplier = 2 ** $task->attempts;
        $calculatedBackoff = min($this->maxBackoff, $this->baseBackoff * $multiplier);
        $randomDelay = (mt_rand(0, 1000) / 1000.0) * $calculatedBackoff;

        $task->nextExecutionTime = microtime(true) + $randomDelay;
        $this->heapPush($task, $task->nextExecutionTime);

        return true;
    }

    /**
     * Polls and extracts all tasks that are currently ready for execution.
     *
     * @param float|null $now Current timestamp (defaults to microtime(true))
     * @return array<int, RetryTask> Ready tasks
     */
    public function pollReady(?float $now = null): array
    {
        $now = $now ?? microtime(true);
        $ready = [];

        while (!empty($this->heap)) {
            $nextDue = $this->heap[0]['priority'];
            if ($nextDue > $now) {
                break;
            }

            $task = $this->heapPop();
            if ($task !== null && isset($this->taskIndex[$task->id])) {
                $ready[] = $task;
            }
        }

        return $ready;
    }

    /**
     * Returns the earliest due timestamp of scheduled tasks, or null if empty.
     */
    public function peekNextDueTime(): ?float
    {
        return empty($this->heap) ? null : $this->heap[0]['priority'];
    }

    public function getDeadLetterQueue(): array
    {
        return $this->deadLetterQueue;
    }

    public function count(): int
    {
        return count($this->taskIndex);
    }

    public function dlqCount(): int
    {
        return count($this->deadLetterQueue);
    }

    private function heapPush(RetryTask $task, float $priority): void
    {
        $this->heap[] = ['priority' => $priority, 'task' => $task];
        $idx = count($this->heap) - 1;

        while ($idx > 0) {
            $parent = intdiv($idx - 1, 2);
            if ($this->heap[$idx]['priority'] < $this->heap[$parent]['priority']) {
                $tmp = $this->heap[$idx];
                $this->heap[$idx] = $this->heap[$parent];
                $this->heap[$parent] = $tmp;
                $idx = $parent;
            } else {
                break;
            }
        }
    }

    private function heapPop(): ?RetryTask
    {
        if (empty($this->heap)) {
            return null;
        }

        $root = $this->heap[0]['task'];
        $last = array_pop($this->heap);

        if (!empty($this->heap)) {
            $this->heap[0] = $last;
            $this->siftDown(0);
        }

        return $root;
    }

    private function siftDown(int $idx): void
    {
        $size = count($this->heap);
        while (true) {
            $smallest = $idx;
            $left = 2 * $idx + 1;
            $right = 2 * $idx + 2;

            if ($left < $size && $this->heap[$left]['priority'] < $this->heap[$smallest]['priority']) {
                $smallest = $left;
            }
            if ($right < $size && $this->heap[$right]['priority'] < $this->heap[$smallest]['priority']) {
                $smallest = $right;
            }

            if ($smallest !== $idx) {
                $tmp = $this->heap[$idx];
                $this->heap[$idx] = $this->heap[$smallest];
                $this->heap[$smallest] = $tmp;
                $idx = $smallest;
            } else {
                break;
            }
        }
    }
}
