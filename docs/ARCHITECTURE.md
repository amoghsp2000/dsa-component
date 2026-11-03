# DSA Toolbox Architecture & Integration Guide

## 1. Vision & Architecture Philosophy

Modern engineering challenges frequently require standard algorithmic primitives (caching, rate limiting, deduplication, fast range queries, priority dispatching). However, developers often face an unfortunate dilemma:
- **Pulling in heavy third-party dependencies**: Often bringing hundreds of transitive packages, security vulnerabilities, and deployment baggage just for a cache or rate limiter.
- **Rewriting from scratch poorly**: Leading to subtle concurrency issues, memory leaks, high computational complexity, or unhandled edge cases.
- **Theoretical disconnect**: Most DSA libraries structure code academically (`binary_tree.py`, `graph.py`) without explaining *how* or *where* to apply them in real-world software architectures like billing systems, API gateways, payment processing, or streaming telemetries.

**DSA Toolbox** bridges this gap by providing:
1. **Zero External Dependencies**: Every component is standalone and dependency-free.
2. **Dual Discovery System**:
   - Academic Discovery: Locate by theoretical DSA concept (e.g., Doubly Linked List, Binary Heap, Trie).
   - Problem-Driven Discovery: Locate by concrete engineering requirement (e.g., API Rate Limiting, Payment Deduplication, LRU Eviction).
3. **Multi-Language Equivalence**: Identical algorithmic contracts and behaviors in PHP, JavaScript, Python, and Java.

---

## 2. Directory Layout & Organization

```text
dsa-toolbox/
├── metadata/                  # Machine-readable registry (JSON schemas)
│   ├── categories.json        # 11 Theoretical DSA categories
│   ├── use-cases.json         # 17 Real-World Engineering use cases
│   └── components.json        # 12 Component specifications
├── docs/                      # Human-readable discovery & specs
│   ├── DISCOVERY_BY_DSA.md    # Index by DSA category
│   ├── DISCOVERY_BY_USE_CASE.md # Index by engineering challenge
│   └── components/            # Deep-dive specs with when to use / when not to use
├── php/                       # PHP 8.x standalone implementations & enterprise examples
│   ├── src/                   # Standalone PHP classes (PSR-12, declare(strict_types=1))
│   └── examples/              # Practical integrations (recharge, billing, gateways)
├── javascript/                # Modern JS (ES2020+, Node & Browser, Zero-dependency)
│   ├── src/                   # Exportable classes
│   └── examples/              # Express middlewares, background workers
├── python/                    # Python 3.9+ implementations (PEP 8, Type Hints)
│   ├── src/                   # Zero-dependency modules
│   └── examples/              # Payment deduplication, streaming metrics
├── java/                      # Java 11+ implementations (Generics, com.dsatoolbox package)
│   ├── src/main/java/com/dsatoolbox/
│   └── examples/
├── tests/                     # Comprehensive test suites for all 4 languages
└── scripts/                   # Validation and catalog generation automation
```

---

## 3. Language Integration Conventions

### PHP Integration
- Every PHP component uses `declare(strict_types=1);` and is enclosed within the `DsaToolbox` namespace.
- Simply copy the file into your existing project (e.g., `app/Libraries/Dsa/` or `src/`) and either autoload via PSR-4 or `require_once`:
  ```php
  require_once __DIR__ . '/LRUCache.php';
  use DsaToolbox\LRUCache;

  $cache = new LRUCache(1000);
  ```

### JavaScript / Node.js Integration
- Modern ES6 classes compatible with both CommonJS (`require`) and ES Modules (`import`).
- No external npm packages required. Copy directly into your utility or lib directory:
  ```javascript
  const LRUCache = require('./LRUCache');
  // or: import LRUCache from './LRUCache.js';

  const cache = new LRUCache(1000);
  ```

### Python Integration
- Full PEP 484 type annotations and dataclasses where appropriate.
- Copy into your module folder:
  ```python
  from lru_cache import LRUCache

  cache: LRUCache[str, dict] = LRUCache(capacity=1000)
  ```

### Java Integration
- Standard Java 11+ generic classes packaged under `com.dsatoolbox`.
- Copy into `src/main/java/com/dsatoolbox/` or your internal library:
  ```java
  import com.dsatoolbox.LRUCache;

  LRUCache<String, Transaction> cache = new LRUCache<>(1000);
  ```

---

## 4. Dual-Classification Taxonomy Matrix

| Component Slug | Theoretical DSA Classification | Real-World Engineering Use Case |
|---|---|---|
| `lru-cache` | Hash Tables + Doubly Linked List | Caching and Memory Management, API Processing, Database Optimization |
| `rate-limiter` | Stacks and Queues (Sliding Window Deque) | Rate Limiting & Throttling, API Processing, Financial Abuse Protection |
| `priority-queue` | Trees and Graphs (Binary Min/Max Heap) | Queue & Background Job Processing, Task Scheduling |
| `trie` | Trees and Graphs (Prefix Tree) | Search & Filtering, API Route Matching, Autocomplete |
| `bloom-filter` | Hash Tables + Bit Manipulation | Data Deduplication, Database Optimization, Negative Cache Query Filtering |
| `consistent-hashing` | Searching and Sorting + Hash Ring | Distributed Systems, Cache Sharding, Dynamic Routing |
| `sliding-window` | Arrays and Strings + Monotonic Deque | Real-Time Telemetry, Moving Extremes, Trading Peak Drawdown |
| `union-find` | Disjoint Set Union (Path Compression & Rank) | Networking & Routing, Fraud Ring & Cycle Detection |
| `doubly-linked-list` | Linked Lists | Foundation for Caches, Deques, and History Buffers |
| `binary-search` | Searching and Sorting | Fast Database Lookups, Threshold & Discount Tier Resolution |
| `prefix-sum` | Arrays and Strings | Financial Balance Ledgers, Date-Range Revenue Queries |
| `retry-queue` | Stacks and Queues + Greedy Scheduling | Resilient Payment Webhooks, Recharge Retries with Jitter |

