"""
LRU Cache (Least Recently Used) with Optional TTL

Traditional DSA Category: Hash Tables + Linked Lists
Real-World Engineering Use Cases:
 - Caching and Memory Management (strict memory bounds, high hit ratio)
 - API and HTTP Processing (API response caching, user authorization tokens)
 - Database Optimization (eliminating repetitive database queries)
 - Performance Optimization (sub-millisecond hot data retrieval)

Time Complexity:
 - get: O(1)
 - put: O(1)
 - delete: O(1)
 - has: O(1)
Space Complexity: O(capacity)
"""

import time
from typing import Generic, TypeVar, Optional, Dict, Any

KT = TypeVar("KT")
VT = TypeVar("VT")


class _LRUNode(Generic[KT, VT]):
    __slots__ = ("key", "value", "expires_at", "prev", "next")

    def __init__(self, key: KT, value: VT, expires_at: Optional[float] = None) -> None:
        self.key: KT = key
        self.value: VT = value
        self.expires_at: Optional[float] = expires_at
        self.prev: Optional["_LRUNode[KT, VT]"] = None
        self.next: Optional["_LRUNode[KT, VT]"] = None


class LRUCache(Generic[KT, VT]):
    def __init__(self, capacity: int) -> None:
        if capacity < 1:
            raise ValueError("LRUCache capacity must be greater than 0")
        self.capacity: int = capacity
        self._map: Dict[KT, _LRUNode[KT, VT]] = {}
        self._head: Optional[_LRUNode[KT, VT]] = None  # Most recently used
        self._tail: Optional[_LRUNode[KT, VT]] = None  # Least recently used
        self._eviction_count: int = 0

    def get(self, key: KT) -> Optional[VT]:
        """
        Retrieves a value by key and moves it to the front (MRU).
        Returns None if key does not exist or has expired.
        """
        if key not in self._map:
            return None

        node = self._map[key]
        if node.expires_at is not None and time.time() >= node.expires_at:
            self.delete(key)
            return None

        self._move_to_head(node)
        return node.value

    def put(self, key: KT, value: VT, ttl_seconds: Optional[float] = None) -> None:
        """
        Inserts or updates a key-value entry with an optional TTL.
        Evicts the least recently used element when capacity is exceeded.
        """
        expires_at = (time.time() + ttl_seconds) if ttl_seconds is not None else None

        if key in self._map:
            node = self._map[key]
            node.value = value
            node.expires_at = expires_at
            self._move_to_head(node)
            return

        node = _LRUNode(key, value, expires_at)
        self._map[key] = node
        self._attach_head(node)

        if len(self._map) > self.capacity:
            self._evict_tail()

    def peek(self, key: KT) -> Optional[VT]:
        """Inspects a value without updating its recency position."""
        if key not in self._map:
            return None
        node = self._map[key]
        if node.expires_at is not None and time.time() >= node.expires_at:
            return None
        return node.value

    def has(self, key: KT) -> bool:
        """Checks whether an active, non-expired key exists in cache."""
        return self.get(key) is not None

    def delete(self, key: KT) -> bool:
        """Removes an entry by key."""
        if key not in self._map:
            return False
        node = self._map[key]
        self._detach_node(node)
        del self._map[key]
        return True

    def clear(self) -> None:
        self._map.clear()
        self._head = None
        self._tail = None
        self._eviction_count = 0

    @property
    def eviction_count(self) -> int:
        return self._eviction_count

    def __len__(self) -> int:
        return len(self._map)

    def _move_to_head(self, node: _LRUNode[KT, VT]) -> None:
        if self._head is node:
            return
        self._detach_node(node)
        self._attach_head(node)

    def _attach_head(self, node: _LRUNode[KT, VT]) -> None:
        node.prev = None
        node.next = self._head
        if self._head is not None:
            self._head.prev = node
        self._head = node
        if self._tail is None:
            self._tail = node

    def _detach_node(self, node: _LRUNode[KT, VT]) -> None:
        if node.prev is not None:
            node.prev.next = node.next
        else:
            self._head = node.next

        if node.next is not None:
            node.next.prev = node.prev
        else:
            self._tail = node.prev

        node.prev = None
        node.next = None

    def _evict_tail(self) -> None:
        if self._tail is None:
            return
        lru = self._tail
        self._detach_node(lru)
        del self._map[lru.key]
        self._eviction_count += 1

