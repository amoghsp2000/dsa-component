# DSA Toolbox – PHP

A developer-focused collection of **zero-dependency, copy-paste-ready Data Structures and Algorithms** optimized for PHP 8.0+.

Each component is self-contained in a single file under `src/` and can be copied directly into any PHP project (Laravel, Symfony, WordPress, Yii, or vanilla PHP) without installing external Composer packages.

---

## 1. Quick Start

### Option A: Direct Copy-Paste (Recommended)
Copy any file from `src/` directly into your application:
```php
<?php

require_once __DIR__ . '/LRUCache.php';

use DsaToolbox\LRUCache;

// Bounded in-memory cache with capacity of 1000 items
$cache = new LRUCache(1000);
$cache->put('user_profile_123', ['name' => 'Alice', 'role' => 'Engineer'], ttlSeconds: 300);

$user = $cache->get('user_profile_123');
```

### Option B: Composer PSR-4 Autoloading
Add to your `composer.json`:
```json
{
  "autoload": {
    "psr-4": {
      "DsaToolbox\\": "path/to/dsa-toolbox/php/src/"
    }
  }
}
```

---

## 2. Available Components

| Component | Class Name | Category | Primary Use Cases |
|---|---|---|---|
| **LRU Cache** | `DsaToolbox\LRUCache` | Hash Tables + Linked List | Caching, API Response Caching, Database Optimization |
| **Sliding Window Rate Limiter** | `DsaToolbox\SlidingWindowRateLimiter` | Stacks and Queues | Rate Limiting, API Abuse Prevention, Gateway Traffic Shaping |
| **Priority Queue** | `DsaToolbox\PriorityQueue` | Binary Heap | Background Job Queues, Task Scheduling, Top-K Filtering |
| **Prefix Tree (Trie)** | `DsaToolbox\Trie` | Trees and Graphs | URL Route Matching, Instant Autocomplete, Prefix Filtering |
| **Bloom Filter** | `DsaToolbox\BloomFilter` | Bit Manipulation + Hashing | Payment Deduplication, Negative Cache Lookup, DB Pre-filtering |
| **Consistent Hash Ring** | `DsaToolbox\ConsistentHash` | Hash Ring + Binary Search | Distributed Cache Sharding, Sticky Session Routing |
| **Monotonic Queue** | `DsaToolbox\SlidingWindow` | Stacks and Queues | Real-Time Telemetry, Streaming Peak Latency Tracking |
| **Disjoint Set Union** | `DsaToolbox\UnionFind` | Advanced Data Structures | Fraud Ring & Circular Transaction Detection, Cluster Partitioning |
| **Doubly Linked List** | `DsaToolbox\DoublyLinkedList` | Linked Lists | Bidirectional History Buffers, Custom Eviction Queues |
| **Binary Search & Bounds** | `DsaToolbox\BinarySearch` | Searching and Sorting | Fee Bracket Resolution, Discount Tiers, Timestamp Index Queries |
| **Prefix Sum Engine** | `DsaToolbox\PrefixSum` | Arrays and Strings | Financial Ledger Balance Audits, Range Aggregations |
| **Retry Queue with Jitter** | `DsaToolbox\RetryQueue` | Priority Heap + Backoff | Resilient Payment Gateway Retries, Telecom Recharge Dispatch |

---

## 3. Real-World Integration Examples

Practical enterprise code demonstrations are located in `examples/`:

- `examples/recharge_billing_pipeline.php`: Mobile recharge & billing dispatcher prioritizing VIP requests and retrying failed telecom operator calls with exponential backoff and full jitter.
- `examples/payment_deduplication_engine.php`: High-throughput payment processing engine combining Bloom Filter idempotency, circular fraud ring detection with Union-Find, and range balance audits with Prefix Sum.
- `examples/api_rate_limited_gateway.php`: API Gateway demonstrating sub-millisecond Trie route matching coupled with sliding window rate limiting per IP.
- `examples/high_throughput_lru_cache.php`: Multi-node distributed caching cluster with consistent hash sharding and bounded LRU caches with TTL.

---

## 4. Running Tests

Run the standalone PHP test suite:
```bash
php tests/php/test_all.php
```
Zero external testing frameworks (PHPUnit, Pest) required!

