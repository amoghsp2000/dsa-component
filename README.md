# DSA Toolbox – Multi-Language Reusable DSA Library

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![Languages](https://img.shields.io/badge/Languages-PHP%20%7C%20JavaScript%20%7C%20Python%20%7C%20Java-brightgreen.svg)](#supported-languages)
[![Zero Dependencies](https://img.shields.io/badge/Dependencies-Zero-success.svg)](#core-principles)
[![Architecture](https://img.shields.io/badge/Categorization-DSA%20%2B%20Real--World%20Use%20Cases-orange.svg)](#two-way-component-discovery)

**DSA Toolbox** is a developer-focused, open-source collection of **dependency-free, copy-paste-ready Data Structures and Algorithms** across **PHP, JavaScript, Python, and Java**.

Unlike academic algorithm repositories that organize code purely by abstract theory (`tree.py`, `graph.java`), **DSA Toolbox** bridges computer science theory with modern systems engineering by categorizing every component across **two simultaneous classification systems**:
1. **Traditional DSA Category** (Arrays, Hash Maps, Heaps, Linked Lists, Tries, Graphs)
2. **Real-World Engineering Use Cases** (Caching, Rate Limiting, Payment Deduplication, Background Workers, Distributed Sharding, Metric Streaming)

---

## 1. Core Principles

- **Zero External Dependencies**: Every component is standalone. You can copy a single file directly into your codebase without installing Composer, npm, pip, or Maven packages.
- **Production-Grade Engineering**: Includes strict type annotations, comprehensive bounds checking, edge-case coverage, and complexity guarantees.
- **Multi-Language Equivalence**: Identical algorithmic semantics across PHP 8.x, JavaScript (Node & Browser), Python 3.9+, and Java 11+.
- **Practical Integrations**: Production-tested blueprints for recharge pipelines, payment deduplication, API gateways, and distributed cache sharding.

---

## 2. Two-Way Component Discovery

Discover components based on your current need:

### [A. Discover by Real-World Engineering Use Case](docs/DISCOVERY_BY_USE_CASE.md)
*Find algorithms based on the engineering challenge you are trying to solve in your system:*

| Engineering Challenge | Recommended Component | Theoretical DSA Category | Key Production Advantage |
|---|---|---|---|
| **API Rate Limiting & Throttling** | [`SlidingWindowRateLimiter`](docs/components/rate-limiter.md) | Stacks and Queues | Precision sub-second rolling window without boundary reset bursts |
| **Payment Deduplication & Anti-Idempotency** | [`BloomFilter`](docs/components/bloom-filter.md) | Advanced Data Structures | Zero false negatives; drops 99.9% duplicate queries before DB hits |
| **Fraud Rings & Circular Transactions** | [`UnionFind`](docs/components/union-find.md) | Advanced Data Structures | Detects money laundering loops ($A \to B \to C \to A$) in $O(\alpha(N))$ time |
| **Fast Ledger Balance & Revenue Auditing** | [`PrefixSum`](docs/components/prefix-sum.md) | Arrays and Strings | Calculates revenue across arbitrary date intervals in strictly $O(1)$ time |
| **Urgent Task Scheduling & VIP Processing** | [`PriorityQueue`](docs/components/priority-queue.md) | Trees and Graphs | Dispatches emergency tasks and high-priority bills in $O(\log N)$ time |
| **Resilient Third-Party Gateway Retries** | [`RetryQueue`](docs/components/retry-queue.md) | Priority Min-Heap | Exponential backoff with randomized Full Jitter prevents thundering herd |
| **API Path & URL Prefix Route Dispatching** | [`Trie`](docs/components/trie.md) | Trees and Graphs | Deterministic $O(M)$ path matching and predictive autocomplete |
| **Bounded In-Memory Response Caching** | [`LRUCache`](docs/components/lru-cache.md) | Hash Tables + Doubly Linked List | Strict memory limits, $O(1)$ reads/writes/evictions with optional TTL |
| **Distributed Cache Sharding** | [`ConsistentHash`](docs/components/consistent-hashing.md) | Searching & Hash Tables | Virtual node ring minimizes key relocation when scaling server clusters |
| **Sliding Peak Latency & Metric Surges** | [`SlidingWindow`](docs/components/sliding-window.md) | Stacks and Queues | Monotonic deque computes moving window maximums in $O(1)$ amortized time |
| **Fee Tiers & Discount Bracket Resolution** | [`BinarySearch`](docs/components/binary-search.md) | Searching and Sorting | Finds highest matching threshold bracket in logarithmic $O(\log K)$ time |
| **Custom Eviction Deques & History Buffers** | [`DoublyLinkedList`](docs/components/doubly-linked-list.md) | Linked Lists | Constant-time $O(1)$ arbitrary node detachment given pointer reference |

👉 **[Browse Full Use-Case Catalog](docs/DISCOVERY_BY_USE_CASE.md)**

---

### [B. Discover by Traditional DSA Category](docs/DISCOVERY_BY_DSA.md)
*Find components based on classical computer science data structures:*

- **Arrays and Strings**: [`PrefixSum`](docs/components/prefix-sum.md), [`SlidingWindow`](docs/components/sliding-window.md)
- **Hash Tables**: [`LRUCache`](docs/components/lru-cache.md), [`BloomFilter`](docs/components/bloom-filter.md)
- **Linked Lists**: [`DoublyLinkedList`](docs/components/doubly-linked-list.md), [`LRUCache`](docs/components/lru-cache.md)
- **Stacks and Queues**: [`SlidingWindowRateLimiter`](docs/components/rate-limiter.md), [`RetryQueue`](docs/components/retry-queue.md), [`SlidingWindow`](docs/components/sliding-window.md)
- **Trees and Graphs**: [`PriorityQueue`](docs/components/priority-queue.md), [`Trie`](docs/components/trie.md), [`UnionFind`](docs/components/union-find.md)
- **Searching and Sorting**: [`BinarySearch`](docs/components/binary-search.md), [`ConsistentHash`](docs/components/consistent-hashing.md)
- **Advanced Data Structures**: [`BloomFilter`](docs/components/bloom-filter.md), [`ConsistentHash`](docs/components/consistent-hashing.md), [`UnionFind`](docs/components/union-find.md)

👉 **[Browse Full DSA Category Catalog](docs/DISCOVERY_BY_DSA.md)**

---

## 3. Supported Languages

| Language | Directory | Minimum Version | Package Standard |
|---|---|---|---|
| **PHP** | [`php/`](php/) | PHP 8.0+ | PSR-12, `declare(strict_types=1);`, `DsaToolbox\` namespace |
| **JavaScript** | [`javascript/`](javascript/) | ES2020+ / Node.js 14+ | CommonJS & ES Module compatible, Zero-dependency |
| **Python** | [`python/`](python/) | Python 3.9+ | PEP 8, PEP 484 Type Hints, Standard Library Only |
| **Java** | [`java/`](java/) | Java 11+ | Generics, `com.dsatoolbox` package |

---

## 4. Copy-Paste Usage in 10 Seconds

### PHP Example: In-Memory LRU Cache with TTL
```php
<?php
require_once __DIR__ . '/php/src/LRUCache.php';
use DsaToolbox\LRUCache;

$cache = new LRUCache(capacity: 1000);
$cache->put('user:101', ['name' => 'Alice', 'role' => 'Admin'], ttlSeconds: 300);

$user = $cache->get('user:101');
```

### Python Example: Precision Rate Limiting
```python
from rate_limiter import SlidingWindowRateLimiter

limiter = SlidingWindowRateLimiter(max_requests=5, window_seconds=10.0)
if not limiter.allow("client_ip_192.168.1.1"):
    print("429 Too Many Requests!")
```

### JavaScript Example: Trie Autocomplete
```javascript
const { Trie } = require('./Trie');

const trie = new Trie();
trie.insert('apple', { id: 1 });
trie.insert('application', { id: 2 });

const suggestions = trie.findWordsWithPrefix('app');
```

### Java Example: Resilient Backoff Retry Queue
```java
import com.dsatoolbox.RetryQueue;

RetryQueue<Payment> rq = new RetryQueue<>(0.5, 30.0);
rq.enqueue("tx_99", new Payment(100.0), 3, 0.0);

for (var task : rq.pollReady()) {
    // Process task...
}
```

---

## 5. Practical Enterprise Integrations

Real-world application blueprints are included across all languages:

1. **[PHP Recharge and Billing Pipeline](php/examples/recharge_billing_pipeline.php)**:
   Prioritizes urgent utility bills ahead of standard prepaid top-ups, resolves cashback tiers in $O(\log K)$ with `BinarySearch`, and retries telecom gateway timeouts using `RetryQueue` with full jitter.
2. **[Payment Deduplication & Fraud Detection Engine](php/examples/payment_deduplication_engine.php)**:
   Pre-screens idempotency keys with `BloomFilter` to spare database load, checks for circular payment fraud loops using `UnionFind`, and audits running balance intervals with `PrefixSum`.
3. **[API Rate Limited Gateway](php/examples/api_rate_limited_gateway.php)**:
   Demonstrates high-throughput URL routing via `Trie` and per-client token consumption via `SlidingWindowRateLimiter`.
4. **[Distributed Consistent-Hashed Cache Cluster](php/examples/high_throughput_lru_cache.php)**:
   Dynamically shards user sessions across a virtual node ring using `ConsistentHash` and local `LRUCache` instances.
5. **[Express Rate Limiting Middleware](javascript/examples/express_rate_limiter_middleware.js)**:
   Zero-dependency Express/Node.js middleware with `Retry-After` HTTP headers.
6. **[Background Job Worker](javascript/examples/job_worker_priority_queue.js)**:
   Node.js priority worker executing background billing events and isolating poisoned tasks into a Dead Letter Queue (DLQ).
7. **[Python Streaming Telemetry Pipeline](python/examples/metrics_sliding_window.py)**:
   Computes moving window peak latencies and interval averages in real-time.

---

## 6. Repository Layout

```text
dsa-toolbox/
├── metadata/                  # Central machine-readable metadata registry
│   ├── categories.json        # 11 Traditional Computer Science DSA categories
│   ├── use-cases.json         # 17 Real-World Engineering use cases
│   └── components.json        # 12 Component metadata specifications
├── docs/                      # Dual discovery documentation & specs
│   ├── DISCOVERY_BY_DSA.md    # Catalog organized by theoretical DSA concept
│   ├── DISCOVERY_BY_USE_CASE.md # Catalog organized by engineering challenge
│   ├── ARCHITECTURE.md        # Architectural vision and integration guide
│   └── components/            # Dedicated documentation for all 12 components
├── php/                       # PHP 8.0+ standalone source & enterprise examples
│   ├── src/                   # Standalone PHP classes
│   └── examples/              # Practical recharge, billing, and payment demos
├── javascript/                # Modern ES6+ / Node.js standalone source & examples
│   ├── src/                   # CommonJS & ES Module compatible classes
│   └── examples/              # Express middlewares & background workers
├── python/                    # Python 3.9+ implementations (PEP 8 + Type Hints)
│   ├── src/                   # Zero-dependency Python modules
│   └── examples/              # Payment deduplication & streaming metrics
├── java/                      # Java 11+ generic classes under com.dsatoolbox
│   ├── src/main/java/com/dsatoolbox/
│   └── examples/              # Enterprise recharge billing demo
├── tests/                     # Zero-dependency unit test suites for all 4 languages
│   ├── php/test_all.php
│   ├── javascript/test_all.js
│   ├── python/test_all.py
│   └── java/TestAll.java
├── scripts/                   # Validation & documentation sync tools
│   ├── validate_metadata.py
│   ├── generate_catalog.py
│   └── run_python_tests.py
├── CONTRIBUTING.md            # Guidelines for adding new components
├── LICENSE                    # MIT License
└── README.md                  # Master documentation
```

---

## 7. Running Tests

Every language includes a standalone test suite with **zero external dependencies**:

- **Python**:
  ```bash
  python scripts/run_python_tests.py
  ```
- **PHP**:
  ```bash
  php tests/php/test_all.php
  ```
- **JavaScript (Node.js)**:
  ```bash
  node tests/javascript/test_all.js
  ```
- **Java**:
  ```bash
  javac -d out java/src/main/java/com/dsatoolbox/*.java tests/java/TestAll.java
  java -cp out com.dsatoolbox.tests.TestAll
  ```

---

## 8. License & Contributing

DSA Toolbox is open-source software licensed under the [MIT License](LICENSE). Contributions following our [Contributing Guidelines](CONTRIBUTING.md) are warmly welcomed!

