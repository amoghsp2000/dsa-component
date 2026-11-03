"""
Monotonic Queue (Sliding Window Extremes Engine)

Traditional DSA Category: Stacks and Queues (Monotonic Deque) + Arrays and Strings
Real-World Engineering Use Cases:
 - Real-Time Data Processing (streaming metrics, market ticker peaks)
 - Logging and Monitoring (sliding 5-minute peak latency or error rate)
 - Financial and Payment Systems (sliding drawdown, high-frequency surge detection)
 - Performance Optimization (computing window extremes in O(1) amortized vs O(K) linear)

Time Complexity:
 - push: O(1) amortized
 - pop: O(1)
 - get_max: O(1)
Space Complexity: O(K) where K is sliding window size
"""

from collections import deque
from typing import Deque, List, Union

Number = Union[int, float]


class SlidingWindow:
    def __init__(self) -> None:
        self._queue: Deque[Number] = deque()
        self._max_deque: Deque[Number] = deque()

    def push(self, val: Number) -> None:
        """Pushes a new value into the sliding window in O(1) amortized time."""
        self._queue.append(val)

        while self._max_deque and self._max_deque[-1] < val:
            self._max_deque.pop()

        self._max_deque.append(val)

    def pop(self) -> Number:
        """Pops the oldest value from the front of the sliding window."""
        if not self._queue:
            raise IndexError("Sliding window is empty")

        val = self._queue.popleft()
        if self._max_deque and self._max_deque[0] == val:
            self._max_deque.popleft()

        return val

    def get_max(self) -> Number:
        """Returns the maximum value in the current window in strictly O(1) time."""
        if not self._max_deque:
            raise IndexError("Sliding window is empty")
        return self._max_deque[0]

    def is_empty(self) -> bool:
        return len(self._queue) == 0

    def __len__(self) -> int:
        return len(self._queue)

    @staticmethod
    def compute_sliding_max(nums: List[Number], k: int) -> List[Number]:
        """
        Computes sliding window maximums across an array of size N with window size K in O(N) total time.
        """
        n = len(nums)
        if n == 0 or k <= 0:
            return []
        if k > n:
            k = n

        result: List[Number] = []
        d: Deque[int] = deque()

        for i in range(n):
            while d and d[0] < i - k + 1:
                d.popleft()

            while d and nums[d[-1]] <= nums[i]:
                d.pop()

            d.append(i)

            if i >= k - 1:
                result.append(nums[d[0]])

        return result

