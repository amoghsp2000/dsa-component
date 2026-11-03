# LRU Cache (Least Recently Used)

> High-performance fixed-capacity key-value cache combining an associative hash map with an intrusive doubly linked list to achieve strict $O(1)$ reads, writes, and evictions.

---

## 1. Classification

- **Traditional DSA Category**: Hash Tables & Linked Lists
- **Real-World Engineering Use Cases**:
  - [Caching and Memory Management](../DISCOVERY_BY_USE_CASE.md#caching-and-memory-management)
  - [API and HTTP Processing](../DISCOVERY_BY_USE_CASE.md#api-and-http-processing)
  - [Database Optimization](../DISCOVERY_BY_USE_CASE.md#database-optimization)
  - [Performance Optimization](../DISCOVERY_BY_USE_CASE.md#performance-optimization)

---

## 2. Complexity Analysis

| Operation | Time Complexity | Space Complexity |
|---|---|---|
| `get(key)` | **O(1)** | O(1) |
| `put(key, value, ttl)` | **O(1)** | O(1) amortized |
| `delete(key)` | **O(1)** | O(1) |
| `peek(key)` | **O(1)** | O(1) |
| **Total Memory Footprint** | - | **O(Capacity)** |

---

## 3. Engineering Guidance

### When to Use
- **Strict Memory Ceilings**: When an application process must never exceed a fixed memory boundary to avoid Out-Of-Memory (OOM) crashes.
- **Temporal Locality**: When recently accessed records (e.g. active user profiles, recharge plans, API tokens) are significantly more likely to be accessed again.
- **Preventing Cache Stampedes**: Buffering expensive SQL query results or external third-party API responses.

### When NOT to Use
- **Uniform Random Access**: If queries are uniformly distributed without temporal clustering, eviction overhead yields poor cache hit ratios.
- **Frequency-Dominant Workloads**: If items accessed thousands of times should remain cached over an item accessed once just now, use an LFU (Least Frequently Used) cache instead.

### Key Edge Cases Handled
1. Capacity equal to 1.
2. Updating an existing key's value without incrementing the item count.
3. Automated eviction of expired keys when TTL is reached.
4. Distinguishing `null` stored values from missing keys.

---

## 4. Implementations & Quick-Copy

### PHP (8.0+)
```php
require_once __DIR__ . '/php/src/LRUCache.php';
use DsaToolbox\LRUCache;

$cache = new LRUCache(1000);
$cache->put('session:user:42', ['role' => 'admin', 'name' => 'Alice'], ttlSeconds: 300);

$session = $cache->get('session:user:42');
```
👉 [Full Standalone PHP Implementation](../../php/src/LRUCache.php)

### Python (3.9+)
```python
from lru_cache import LRUCache

cache = LRUCache[str, dict](capacity=1000)
cache.put("session:user:42", {"role": "admin", "name": "Alice"}, ttl_seconds=300)

session = cache.get("session:user:42")
```
👉 [Full Standalone Python Implementation](../../python/src/lru_cache.py)

### JavaScript (ES2020+)
```javascript
const { LRUCache } = require('./LRUCache');

const cache = new LRUCache(1000);
cache.put('session:user:42', { role: 'admin', name: 'Alice' }, 300);

const session = cache.get('session:user:42');
```
👉 [Full Standalone JavaScript Implementation](../../javascript/src/LRUCache.js)

### Java (11+)
```java
import com.dsatoolbox.LRUCache;

LRUCache<String, UserProfile> cache = new LRUCache<>(1000);
cache.put("session:user:42", new UserProfile("Alice", "admin"), 300L);

UserProfile session = cache.get("session:user:42");
```
👉 [Full Standalone Java Implementation](../../java/src/main/java/com/dsatoolbox/LRUCache.java)

---

## 5. Practical Enterprise Example
See [Distributed Consistent-Hashed LRU Cache Cluster](../../php/examples/high_throughput_lru_cache.php) demonstrating multi-node cache partitioning.

