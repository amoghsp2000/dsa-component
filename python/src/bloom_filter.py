"""
Bloom Filter

Traditional DSA Category: Advanced Data Structures (Bit Manipulation + Hashing)
Real-World Engineering Use Cases:
 - Data Deduplication and Validation (idempotency key pre-filtering before DB queries)
 - Database Optimization (eliminating disk reads for non-existent records / anti-cache penetration)
 - Financial and Payment Systems (checking transaction references, duplicate order prevention)

Guarantees:
 - False Negatives: NEVER (if contains() returns False, item is 100% not in set)
 - False Positives: Tunable probability (e.g. 1%, 0.1%)

Time Complexity:
 - add: O(K) where K is number of hash iterations
 - contains: O(K)
Space Complexity: O(M) bits
"""

import math
import hashlib
from typing import Tuple


class BloomFilter:
    def __init__(self, expected_elements: int, false_positive_rate: float = 0.01) -> None:
        if expected_elements < 1:
            raise ValueError("expected_elements must be at least 1")
        if not (0.0 < false_positive_rate < 1.0):
            raise ValueError("false_positive_rate must be strictly between 0 and 1")

        # Optimal bit size: m = -(n * ln(p)) / (ln(2)^2)
        raw_m = math.ceil(-(expected_elements * math.log(false_positive_rate)) / (math.log(2) ** 2))
        self.bit_size: int = max(64, math.ceil(raw_m / 8) * 8)

        # Optimal hash count: k = (m / n) * ln(2)
        self.hash_count: int = max(1, round((self.bit_size / expected_elements) * math.log(2)))

        self._byte_array: bytearray = bytearray(self.bit_size // 8)
        self._item_count: int = 0

    def add(self, item: str) -> None:
        """Adds an item to the Bloom Filter."""
        h1, h2 = self._hash_pair(item)

        for i in range(self.hash_count):
            bit_index = (h1 + i * h2) % self.bit_size
            self._set_bit(bit_index)

        self._item_count += 1

    def contains(self, item: str) -> bool:
        """
        Tests whether item is possibly in the set.
        Returns False if item is DEFINITIVELY NOT present.
        Returns True if item is PROBABLY present.
        """
        h1, h2 = self._hash_pair(item)

        for i in range(self.hash_count):
            bit_index = (h1 + i * h2) % self.bit_size
            if not self._test_bit(bit_index):
                return False

        return True

    @property
    def item_count(self) -> int:
        return self._item_count

    def estimated_false_positive_rate(self) -> float:
        """Calculates theoretical error rate based on current element count."""
        if self._item_count == 0:
            return 0.0
        return (1.0 - math.exp(-(self.hash_count * self._item_count) / self.bit_size)) ** self.hash_count

    def clear(self) -> None:
        self._byte_array = bytearray(self.bit_size // 8)
        self._item_count = 0

    def _set_bit(self, index: int) -> None:
        byte_pos = index // 8
        bit_pos = index % 8
        self._byte_array[byte_pos] |= (1 << bit_pos)

    def _test_bit(self, index: int) -> bool:
        byte_pos = index // 8
        bit_pos = index % 8
        return (self._byte_array[byte_pos] & (1 << bit_pos)) != 0

    def _hash_pair(self, item: str) -> Tuple[int, int]:
        digest = hashlib.md5(item.encode("utf-8")).digest()
        h1 = int.from_bytes(digest[:8], byteorder="big", signed=False)
        h2 = int.from_bytes(digest[8:16], byteorder="big", signed=False)
        return h1, h2

