# Consistent Hash Ring

> Distributed hashing scheme mapping keys and servers to a circular hash ring using virtual nodes, ensuring minimal key relocation when nodes are added or removed.

---

## 1. Classification

- **Traditional DSA Category**: Advanced Data Structures & Searching and Sorting
- **Real-World Engineering Use Cases**:
  - [Distributed Systems](../DISCOVERY_BY_USE_CASE.md#distributed-systems)
  - [Caching and Memory Management](../DISCOVERY_BY_USE_CASE.md#caching-and-memory-management)
  - [Networking and Routing](../DISCOVERY_BY_USE_CASE.md#networking-and-routing)

---

## 2. Complexity Analysis

| Operation | Time Complexity | Space Complexity |
|---|---|---|
| `addNode(node)` | **O(V × log(N × V))** (where V is virtual nodes) | O(V) |
| `removeNode(node)` | **O(V × log(N × V))** | O(V) |
| `getNode(key)` | **O(log(N × V))** (via Binary Search on ring) | O(1) |
| **Total Memory Footprint** | - | **O(N × V)** |

---

## 3. Engineering Guidance

### When to Use
- **Cache Sharding**: Distributing user sessions or database query caches across a dynamic fleet of Redis or Memcached servers.
- **Minimizing Cache Miss Cascades**: When a cache server crashes or scales up, traditional modulo hashing (`hash % N`) relocates almost 100% of keys; consistent hashing relocates only $\frac{1}{N}$ of keys.
- **Sticky Gateway Routing**: Routing traffic from specific client IPs or account IDs to dedicated backend services.

### When NOT to Use
- **Single Node Deployments**: Unnecessary overhead when running without multiple partitioned nodes.

---

## 4. Implementations & Quick-Copy

### PHP (8.0+)
```php
require_once __DIR__ . '/php/src/ConsistentHash.php';
use DsaToolbox\ConsistentHash;

$ring = new ConsistentHash(replicas: 100);
$ring->addNode('redis-node-1');
$ring->addNode('redis-node-2');

$server = $ring->getNode('session_user_88291');
```
👉 [Full Standalone PHP Implementation](../../php/src/ConsistentHash.php)

### Python (3.9+)
```python
from consistent_hash import ConsistentHash

ring = ConsistentHash(replicas=100)
ring.add_node("redis-node-1")
ring.add_node("redis-node-2")

server = ring.get_node("session_user_88291")
```
👉 [Full Standalone Python Implementation](../../python/src/consistent_hash.py)

### JavaScript (ES2020+)
```javascript
const { ConsistentHash } = require('./ConsistentHash');

const ring = new ConsistentHash(100);
ring.addNode('redis-node-1');
ring.addNode('redis-node-2');

const server = ring.getNode('session_user_88291');
```
👉 [Full Standalone JavaScript Implementation](../../javascript/src/ConsistentHash.js)

### Java (11+)
```java
import com.dsatoolbox.ConsistentHash;

ConsistentHash ring = new ConsistentHash(100);
ring.addNode("redis-node-1");
ring.addNode("redis-node-2");

String server = ring.getNode("session_user_88291");
```
👉 [Full Standalone Java Implementation](../../java/src/main/java/com/dsatoolbox/ConsistentHash.java)

---

## 5. Practical Enterprise Examples
- [PHP High-Throughput Distributed Cache Cluster](../../php/examples/high_throughput_lru_cache.php)

