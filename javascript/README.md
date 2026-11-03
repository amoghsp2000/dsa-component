# DSA Toolbox – JavaScript / Node.js

A developer-focused collection of **zero-dependency, copy-paste-ready Data Structures and Algorithms** for Modern JavaScript (Node.js & Modern Browsers).

Each component is self-contained in `src/` and supports both CommonJS (`require`) and ES Modules (`import`).

---

## 1. Quick Start

### Direct Copy-Paste (Recommended)
Copy any file from `src/` directly into your project:
```javascript
const { LRUCache } = require('./LRUCache');

// Bounded in-memory cache with capacity 1000 and optional TTL
const cache = new LRUCache(1000);
cache.put('session_45', { userId: 45, role: 'Admin' }, 300); // 300s TTL

const session = cache.get('session_45');
```

---

## 2. Available Components

| Component | Class Name | Category | Primary Engineering Use Cases |
|---|---|---|---|
| **LRU Cache** | `LRUCache` | Hash Tables + Linked List | Caching, API Response Caching, Memory Bounds |
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
- `examples/express_rate_limiter_middleware.js`: Express/Node.js rate limiting middleware enforcing sliding window limits and 429 Retry-After headers.
- `examples/job_worker_priority_queue.js`: Background job worker executing high-priority tasks and re-scheduling failed tasks with exponential backoff.

---

## 4. Running Tests

Run the zero-dependency test suite in Node.js:
```bash
node tests/javascript/test_all.js
```
No external test runners or npm packages needed.

