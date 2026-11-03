"""
Retry Queue with Exponential Backoff & Full Jitter

Traditional DSA Category: Stacks and Queues (Priority Min-Heap) + Greedy Algorithms
Real-World Engineering Use Cases:
 - Queue and Background Job Processing (resilient task retries, worker orchestration)
 - Financial and Payment Systems (payment gateway webhooks, recharge retries)
 - API and HTTP Processing (mitigating thundering herd during third-party outages)

Backoff Formula (Full Jitter):
  sleep = random_float(0, min(max_backoff, base_backoff * (2 ^ attempt)))

Time Complexity:
 - enqueue: O(log N)
 - poll_ready: O(log N) per ready item, O(1) peek when none ready
 - retry: O(log N)
Space Complexity: O(N)
"""

import time
import random
from typing import Dict, List, Optional, Any, Tuple


class RetryTask:
    __slots__ = ("id", "payload", "attempts", "max_attempts", "next_execution_time", "last_error")

    def __init__(
        self,
        task_id: str,
        payload: Any,
        max_attempts: int,
        next_execution_time: float
    ) -> None:
        self.id: str = task_id
        self.payload: Any = payload
        self.attempts: int = 0
        self.max_attempts: int = max_attempts
        self.next_execution_time: float = next_execution_time
        self.last_error: Optional[str] = None


class RetryQueue:
    def __init__(self, base_backoff: float = 1.0, max_backoff: float = 60.0) -> None:
        if base_backoff <= 0.0 or max_backoff < base_backoff:
            raise ValueError("Invalid backoff: base must be > 0 and <= max")

        self.base_backoff: float = base_backoff
        self.max_backoff: float = max_backoff
        self._heap: List[Tuple[float, RetryTask]] = []  # Min-heap by next_execution_time
        self._task_index: Dict[str, RetryTask] = {}
        self._dead_letter_queue: List[RetryTask] = []

    def enqueue(
        self,
        task_id: str,
        payload: Any,
        max_attempts: int = 5,
        delay_seconds: float = 0.0
    ) -> None:
        """Enqueues a task for execution."""
        now = time.time()
        due = now + max(0.0, delay_seconds)

        task = RetryTask(task_id, payload, max_attempts, due)
        self._task_index[task_id] = task
        self._heap_push(task, due)

    def retry(self, task_id: str, reason: Optional[str] = None) -> bool:
        """
        Re-schedules a failed task with exponential backoff and randomized full jitter.
        If task reaches max_attempts, moves it to Dead Letter Queue (DLQ).
        """
        if task_id not in self._task_index:
            return False

        task = self._task_index[task_id]
        task.attempts += 1
        task.last_error = reason

        if task.attempts >= task.max_attempts:
            self._dead_letter_queue.append(task)
            del self._task_index[task_id]
            return False

        multiplier = 2 ** task.attempts
        calculated_backoff = min(self.max_backoff, self.base_backoff * multiplier)
        random_delay = random.uniform(0, calculated_backoff)

        task.next_execution_time = time.time() + random_delay
        self._heap_push(task, task.next_execution_time)
        return True

    def poll_ready(self, now: Optional[float] = None) -> List[RetryTask]:
        """Polls and extracts all tasks currently ready for execution."""
        curr = now if now is not None else time.time()
        ready: List[RetryTask] = []

        while self._heap:
            next_due, task = self._heap[0]
            if next_due > curr:
                break

            self._heap_pop()
            if task.id in self._task_index:
                ready.append(task)

        return ready

    def peek_next_due_time(self) -> Optional[float]:
        return self._heap[0][0] if self._heap else None

    @property
    def dead_letter_queue(self) -> List[RetryTask]:
        return self._dead_letter_queue

    def __len__(self) -> int:
        return len(self._task_index)

    @property
    def dlq_count(self) -> int:
        return len(self._dead_letter_queue)

    def _heap_push(self, task: RetryTask, priority: float) -> None:
        self._heap.append((priority, task))
        idx = len(self._heap) - 1
        while idx > 0:
            parent = (idx - 1) // 2
            if self._heap[idx][0] < self._heap[parent][0]:
                self._heap[idx], self._heap[parent] = self._heap[parent], self._heap[idx]
                idx = parent
            else:
                break

    def _heap_pop(self) -> Optional[RetryTask]:
        if not self._heap:
            return None
        root = self._heap[0][1]
        last = self._heap.pop()
        if self._heap:
            self._heap[0] = last
            self._sift_down(0)
        return root

    def _sift_down(self, idx: int) -> None:
        size = len(self._heap)
        while True:
            smallest = idx
            left = 2 * idx + 1
            right = 2 * idx + 2

            if left < size and self._heap[left][0] < self._heap[smallest][0]:
                smallest = left
            if right < size and self._heap[right][0] < self._heap[smallest][0]:
                smallest = right

            if smallest != idx:
                self._heap[idx], self._heap[smallest] = self._heap[smallest], self._heap[idx]
                idx = smallest
            else:
                break
