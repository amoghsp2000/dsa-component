# DSA Toolbox – Python

A developer-focused collection of **zero-dependency, copy-paste-ready Data Structures and Algorithms** optimized for Python 3.9+.

Each component is written with PEP 8 standards, PEP 484 type hints, and zero external requirements outside the Python standard library.

---

## 1. Quick Start

### Direct Copy-Paste (Recommended)
Copy any file from `src/` directly into your project:
```python
from lru_cache import LRUCache

# Create an LRU cache with capacity 1000 and optional TTL
cache = LRUCache[str, dict](capacity=1000)
cache.put("user_101", {"name": "Alice", "role": "Admin"}, ttl_seconds=300)

user = cache.get("user_101")
```

---

## 2. Available Components

| Component | Class Name | Category | Primary Engineering Use Cases |
|---|---|---|---|
| **LRU Cache** | `LRUCache` | Hash Tables + Doubly Linked List | Caching, API Response Caching, Memory Bounds |
| **Sliding Window Rate Limiter** | `SlidingWindowRateLimiter` | Stacks and Queues | API Rate Limiting, Abuse Prevention |
| **Priority Queue** | `PriorityQueue` | Binary Heap | Background Job Queues, Task Scheduling |
| **Prefix Tree (Trie)** | `Trie` | Trees and Graphs | URL Route Matching, Autocomplete Suggestions |
| **Bloom Filter** | `BloomFilter` | Bit Manipulation + Hashing | Payment Deduplication, Cache Penetration Shield |
| **Consistent Hash Ring** | `ConsistentHash` | Hash Ring + Binary Search | Distributed Cache Sharding, Gateway Sharding |
| **Monotonic Queue** | `SlidingWindow` | Stacks and Queues | Real-Time Telemetry, Streaming Peak Latencies |
| **Disjoint Set Union** | `UnionFind` | Advanced Data Structures | Fraud Ring Detection, Cycle Detection |
| **Doubly Linked List** | `DoublyLinkedList` | Linked Lists | Custom Deques, Eviction Foundations |
| **Binary Search & Bounds** | `BinarySearch` | Searching and Sorting | Fee Bracket Resolution, Sorted Lookups |
| **Prefix Sum Engine** | `PrefixSum` | Arrays and Strings | Financial Ledger Auditing, Range Queries |
| **Retry Queue with Jitter** | `RetryQueue` | Priority Min-Heap | Resilient Webhook Retries, Backoff with Jitter |

---

## 3. Integration Examples

Explore realistic scripts in `examples/`:
- `examples/payment_dedup_service.py`: High-throughput payment processing combining BloomFilter idempotency pre-screening and UnionFind circular fraud detection.
- `examples/metrics_sliding_window.py`: Real-time streaming metrics processing using Monotonic Deque and PrefixSum interval queries.

---

## 4. Running Tests

Run the zero-dependency test suite via standard library `unittest`:
```bash
python scripts/run_python_tests.py
```
All tests run with 100% test pass rate in milliseconds.

