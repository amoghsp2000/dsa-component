# DSA Toolbox – Java

A developer-focused collection of **zero-dependency, copy-paste-ready Data Structures and Algorithms** optimized for Java 11+.

Packaged under `com.dsatoolbox`, each class is standalone and can be copied directly into any Java project (Spring Boot, Quarkus, Micronaut, Android, or plain Java) without needing external Maven or Gradle dependencies.

---

## 1. Quick Start

### Direct Copy-Paste (Recommended)
Copy any file from `src/main/java/com/dsatoolbox/` directly into your Java source directory:
```java
import com.dsatoolbox.LRUCache;

public class App {
    public static void main(String[] args) {
        // Bounded in-memory cache with capacity 1000 and 300s TTL
        LRUCache<String, UserProfile> cache = new LRUCache<>(1000);
        cache.put("user_101", new UserProfile("Alice", "Engineering"), 300L);

        UserProfile profile = cache.get("user_101");
    }
}
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

Explore realistic implementations in `examples/`:
- `examples/RechargeBillingDemo.java`: Enterprise recharge and billing pipeline using BinarySearch tier resolution, BloomFilter idempotency pre-screening, and PriorityQueue urgent task dispatching.

---

## 4. Compiling and Testing

Compile and run the zero-dependency test suite:
```bash
javac -d out java/src/main/java/com/dsatoolbox/*.java tests/java/TestAll.java
java -cp out com.dsatoolbox.tests.TestAll
```
Zero build tools required.

