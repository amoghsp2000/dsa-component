# Disjoint Set Union (Union-Find)

> High-efficiency partition tracking structure with path compression and union-by-rank, providing near-constant time $O(\alpha(N))$ equivalence queries and set mergers.

---

## 1. Classification

- **Traditional DSA Category**: Advanced Data Structures & Trees and Graphs
- **Real-World Engineering Use Cases**:
  - [Networking and Routing](../DISCOVERY_BY_USE_CASE.md#networking-and-routing)
  - [Distributed Systems](../DISCOVERY_BY_USE_CASE.md#distributed-systems)
  - [Transaction Processing](../DISCOVERY_BY_USE_CASE.md#transaction-processing)

---

## 2. Complexity Analysis

| Operation | Time Complexity | Space Complexity |
|---|---|---|
| `find(x)` | **O(α(N))** (effectively constant time < 5 operations) | O(1) amortized |
| `union(x, y)` | **O(α(N))** | O(1) |
| `connected(x, y)` | **O(α(N))** | O(1) |
| **Total Memory Footprint** | - | **O(N)** |

---

## 3. Engineering Guidance

### When to Use
- **Detecting Circular Money Laundering / Fraud Rings**: When a sequence of payment transfers ($A \to B \to C \to A$) forms a cycle, `union(x, y)` returns `false`, immediately flagging the circular flow.
- **Dynamic Cluster Connectivity**: Tracking active network mesh routes or detecting if network partitions have split connected services.
- **Kruskal's Algorithm**: Finding minimum spanning trees for server wiring or routing.

### When NOT to Use
- **Directed Graph Pathfinding**: Classical Union-Find models undirected equivalence relationships; for directed shortest paths, use BFS, Dijkstra, or Bellman-Ford.
- **Dynamic Set Splitting**: Removing or disassociating elements after union is not supported.

---

## 4. Implementations & Quick-Copy

### PHP (8.0+)
```php
require_once __DIR__ . '/php/src/UnionFind.php';
use DsaToolbox\UnionFind;

$uf = new UnionFind();
$uf->union('Account_A', 'Account_B');
$uf->union('Account_B', 'Account_C');

if (!$uf->union('Account_C', 'Account_A')) {
    // Returns false -> circular cycle detected!
}
```
👉 [Full Standalone PHP Implementation](../../php/src/UnionFind.php)

### Python (3.9+)
```python
from union_find import UnionFind

uf = UnionFind()
uf.union("Account_A", "Account_B")
uf.union("Account_B", "Account_C")

if not uf.union("Account_C", "Account_A"):
    # Circular flow detected
    pass
```
👉 [Full Standalone Python Implementation](../../python/src/union_find.py)

### JavaScript (ES2020+)
```javascript
const { UnionFind } = require('./UnionFind');

const uf = new UnionFind();
uf.union('Account_A', 'Account_B');
uf.union('Account_B', 'Account_C');

if (!uf.union('Account_C', 'Account_A')) {
  // Cycle detected!
}
```
👉 [Full Standalone JavaScript Implementation](../../javascript/src/UnionFind.js)

### Java (11+)
```java
import com.dsatoolbox.UnionFind;

UnionFind uf = new UnionFind();
uf.union("Account_A", "Account_B");
uf.union("Account_B", "Account_C");

if (!uf.union("Account_C", "Account_A")) {
    // Cycle detected
}
```
👉 [Full Standalone Java Implementation](../../java/src/main/java/com/dsatoolbox/UnionFind.java)

---

## 5. Practical Enterprise Examples
- [PHP Payment Fraud Ring Detection Engine](../../php/examples/payment_deduplication_engine.php)
- [Python Payment Deduplication and Fraud Detection](../../python/examples/payment_dedup_service.py)

