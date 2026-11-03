"""
Sliding Window Rate Limiter

Traditional DSA Category: Stacks and Queues (Sliding Window Deque)
Real-World Engineering Use Cases:
 - Rate Limiting and Throttling (API quota enforcement, anti-scraping)
 - API and HTTP Processing (protecting upstream services against surges)
 - Financial and Payment Systems (preventing rapid double-spending or card stuffing)

Time Complexity:
 - allow: O(K) amortized, where K is expired timestamps in the window
 - get_remaining: O(1)
 - get_reset_time: O(1)
Space Complexity: O(U * M) where U is active users and M is max requests per window
"""

import time
from collections import deque
from typing import Dict, Deque, Optional


class SlidingWindowRateLimiter:
    def __init__(self, max_requests: int, window_seconds: float) -> None:
        if max_requests < 1:
            raise ValueError("max_requests must be at least 1")
        if window_seconds <= 0.0:
            raise ValueError("window_seconds must be greater than 0")

        self.max_requests: int = max_requests
        self.window_seconds: float = window_seconds
        self._request_logs: Dict[str, Deque[float]] = {}

    def allow(self, key: str, now: Optional[float] = None) -> bool:
        """
        Attempts to consume a token for key.
        Returns True if request is permitted, False if rate limited.
        """
        curr = now if now is not None else time.time()
        window_start = curr - self.window_seconds

        self._cleanup_key(key, window_start)

        if key not in self._request_logs:
            self._request_logs[key] = deque()

        if len(self._request_logs[key]) < self.max_requests:
            self._request_logs[key].append(curr)
            return True

        return False

    def get_remaining(self, key: str, now: Optional[float] = None) -> int:
        """Returns the number of remaining permitted requests in the current sliding window."""
        curr = now if now is not None else time.time()
        window_start = curr - self.window_seconds

        self._cleanup_key(key, window_start)
        current_count = len(self._request_logs[key]) if key in self._request_logs else 0
        return max(0, self.max_requests - current_count)

    def get_reset_time(self, key: str, now: Optional[float] = None) -> float:
        """
        Returns the timestamp when the next request token becomes available.
        Returns now if tokens are currently available.
        """
        curr = now if now is not None else time.time()
        window_start = curr - self.window_seconds

        self._cleanup_key(key, window_start)

        if key not in self._request_logs or len(self._request_logs[key]) < self.max_requests:
            return curr

        oldest = self._request_logs[key][0]
        return oldest + self.window_seconds

    def reset(self, key: str) -> None:
        """Resets rate limit records for a given key."""
        if key in self._request_logs:
            del self._request_logs[key]

    def prune(self, now: Optional[float] = None) -> None:
        """Garbage-collects stale keys whose request logs have completely expired."""
        curr = now if now is not None else time.time()
        window_start = curr - self.window_seconds

        stale_keys = []
        for key in self._request_logs:
            self._cleanup_key(key, window_start)
            if not self._request_logs[key]:
                stale_keys.append(key)

        for key in stale_keys:
            del self._request_logs[key]

    def _cleanup_key(self, key: str, window_start: float) -> None:
        if key not in self._request_logs:
            return

        q = self._request_logs[key]
        while q and q[0] <= window_start:
            q.popleft()

