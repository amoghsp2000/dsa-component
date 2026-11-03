"""
Comprehensive Test Suite for DSA Toolbox (Python)
Zero external dependencies - Uses Python standard library unittest.
"""

import unittest
import sys
from pathlib import Path

# Add src to path
SRC_DIR = Path(__file__).resolve().parent.parent.parent / "python" / "src"
sys.path.insert(0, str(SRC_DIR))

from doubly_linked_list import DoublyLinkedList
from binary_search import BinarySearch
from prefix_sum import PrefixSum
from priority_queue import PriorityQueue
from trie import Trie
from union_find import UnionFind
from lru_cache import LRUCache
from rate_limiter import SlidingWindowRateLimiter
from bloom_filter import BloomFilter
from consistent_hash import ConsistentHash
from sliding_window import SlidingWindow
from retry_queue import RetryQueue


class TestDoublyLinkedList(unittest.TestCase):
    def test_operations(self):
        dll = DoublyLinkedList()
        dll.append("B")
        n1 = dll.prepend("A")
        dll.append("C")
        self.assertEqual(len(dll), 3)
        self.assertEqual(dll.to_list(), ["A", "B", "C"])

        dll.remove_node(n1)
        self.assertEqual(dll.to_list(), ["B", "C"])

        popped = dll.pop_last()
        self.assertEqual(popped, "C")
        self.assertEqual(len(dll), 1)

        dll.clear()
        self.assertEqual(len(dll), 0)
        self.assertTrue(dll.is_empty())


class TestBinarySearch(unittest.TestCase):
    def test_search_and_bounds(self):
        arr = [10, 20, 30, 40, 50, 60]
        self.assertEqual(BinarySearch.find(arr, 30), 2)
        self.assertEqual(BinarySearch.find(arr, 35), -1)
        self.assertEqual(BinarySearch.lower_bound(arr, 35), 3)
        self.assertEqual(BinarySearch.upper_bound(arr, 30), 3)

    def test_find_tier(self):
        tiers = [
            {"threshold": 10, "value": "Bronze"},
            {"threshold": 50, "value": "Silver"},
            {"threshold": 100, "value": "Gold"},
        ]
        self.assertEqual(BinarySearch.find_tier(tiers, 75), "Silver")
        self.assertIsNone(BinarySearch.find_tier(tiers, 5))


class TestPrefixSum(unittest.TestCase):
    def test_1d_queries(self):
        ps = PrefixSum([5, 10, 15, 20, 25])
        self.assertEqual(ps.query_range(1, 3), 45)
        self.assertEqual(ps.query_range(0, 0), 5)
        with self.assertRaises(IndexError):
            ps.query_range(-1, 2)
        with self.assertRaises(ValueError):
            ps.query_range(3, 1)

    def test_2d_queries(self):
        matrix = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ]
        ps = PrefixSum()
        ps.build_2d(matrix)
        self.assertEqual(ps.query_2d(1, 1, 2, 2), 28)


class TestPriorityQueue(unittest.TestCase):
    def test_min_and_max_heap(self):
        min_heap = PriorityQueue(is_min_heap=True)
        min_heap.push("Task C", 30)
        min_heap.push("Task A", 10)
        min_heap.push("Task B", 20)
        self.assertEqual(min_heap.pop(), "Task A")
        self.assertEqual(min_heap.pop(), "Task B")
        self.assertEqual(min_heap.pop(), "Task C")

        max_heap = PriorityQueue(is_min_heap=False)
        max_heap.push("Task X", 10)
        max_heap.push("Task Z", 50)
        self.assertEqual(max_heap.pop(), "Task Z")


class TestTrie(unittest.TestCase):
    def test_insert_search_autocomplete(self):
        trie = Trie()
        trie.insert("apple", 100)
        trie.insert("app", 200)
        trie.insert("application", 300)

        self.assertTrue(trie.search("apple"))
        self.assertFalse(trie.search("appl"))
        self.assertTrue(trie.starts_with("app"))
        self.assertEqual(trie.get_value("app"), 200)

        results = trie.find_words_with_prefix("app")
        self.assertEqual(len(results), 3)

        self.assertTrue(trie.delete("app"))
        self.assertFalse(trie.search("app"))
        self.assertTrue(trie.search("apple"))


class TestUnionFind(unittest.TestCase):
    def test_union_and_cycle(self):
        uf = UnionFind()
        self.assertTrue(uf.union("A", "B"))
        self.assertTrue(uf.union("B", "C"))
        self.assertTrue(uf.connected("A", "C"))
        self.assertEqual(uf.get_component_size("A"), 3)
        self.assertFalse(uf.union("A", "C"))  # Cycle detection


class TestLRUCache(unittest.TestCase):
    def test_eviction_and_recency(self):
        cache = LRUCache(capacity=2)
        cache.put("k1", "v1")
        cache.put("k2", "v2")
        self.assertEqual(cache.get("k1"), "v1")  # k1 MRU, k2 LRU

        cache.put("k3", "v3")  # Evicts k2
        self.assertIsNone(cache.get("k2"))
        self.assertEqual(cache.get("k1"), "v1")
        self.assertEqual(cache.get("k3"), "v3")
        self.assertEqual(cache.eviction_count, 1)


class TestRateLimiter(unittest.TestCase):
    def test_sliding_window_allow(self):
        limiter = SlidingWindowRateLimiter(max_requests=2, window_seconds=5.0)
        now = 1000.0
        self.assertTrue(limiter.allow("user1", now=now))
        self.assertTrue(limiter.allow("user1", now=now + 1.0))
        self.assertFalse(limiter.allow("user1", now=now + 2.0))
        self.assertEqual(limiter.get_remaining("user1", now=now + 2.0), 0)

        # After window slides
        self.assertTrue(limiter.allow("user1", now=now + 6.0))


class TestBloomFilter(unittest.TestCase):
    def test_membership_and_no_false_negatives(self):
        bf = BloomFilter(expected_elements=100, false_positive_rate=0.01)
        bf.add("key1")
        bf.add("key2")
        self.assertTrue(bf.contains("key1"))
        self.assertTrue(bf.contains("key2"))
        self.assertFalse(bf.contains("unseen_key"))


class TestConsistentHash(unittest.TestCase):
    def test_node_mapping(self):
        ch = ConsistentHash(replicas=50)
        ch.add_node("server-1")
        ch.add_node("server-2")
        self.assertIn(ch.get_node("user_101"), ["server-1", "server-2"])
        self.assertIn(ch.get_node("user_102"), ["server-1", "server-2"])


class TestSlidingWindow(unittest.TestCase):
    def test_monotonic_extremes(self):
        nums = [1, 3, -1, -3, 5, 3, 6, 7]
        sliding_max = SlidingWindow.compute_sliding_max(nums, 3)
        self.assertEqual(sliding_max, [3, 3, 5, 5, 6, 7])


class TestRetryQueue(unittest.TestCase):
    def test_retry_and_dlq(self):
        rq = RetryQueue(base_backoff=0.1, max_backoff=1.0)
        rq.enqueue("tx_1", {"amount": 50}, max_attempts=2, delay_seconds=0.0)
        self.assertEqual(len(rq), 1)

        ready = rq.poll_ready()
        self.assertEqual(len(ready), 1)

        # First failure -> retry
        self.assertTrue(rq.retry("tx_1", "Network error"))

        # Second failure -> exhausted, moves to DLQ
        self.assertFalse(rq.retry("tx_1", "Permanent error"))
        self.assertEqual(rq.dlq_count, 1)


if __name__ == "__main__":
    unittest.main()

