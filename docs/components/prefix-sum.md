# Prefix Sum & Range Query Engine

> Preprocessing technique storing running cumulative totals to answer range sum queries over arbitrary 1D and 2D intervals in strictly $O(1)$ time.

---

## 1. Classification

- **Traditional DSA Category**: Arrays and Strings
- **Real-World Engineering Use Cases**:
  - [Financial and Payment Systems](../DISCOVERY_BY_USE_CASE.md#financial-and-payment-systems)
  - [Logging and Monitoring](../DISCOVERY_BY_USE_CASE.md#logging-and-monitoring)
  - [Performance Optimization](../DISCOVERY_BY_USE_CASE.md#performance-optimization)

---

## 2. Complexity Analysis

| Operation | Time Complexity | Space Complexity |
|---|---|---|
| `build1D(values)` | **O(N)** | O(N) |
| `queryRange(left, right)` | **O(1)** | O(1) |
| `build2D(matrix)` | **O(R × C)** | O(R × C) |
| `query2D(r1, c1, r2, c2)` | **O(1)** | O(1) |
| **Total Memory Footprint** | - | **O(N)** or **O(R × C)** |

---

## 3. Engineering Guidance

### When to Use
- **Financial Balance & Revenue Auditing**: Calculating the total revenue, recharge volume, or debit/credit delta between any two transaction dates without iterating over millions of entries.
- **Sub-Millisecond Telemetry Aggregation**: Answering windowed telemetry metric sums instantly.
- **Image Processing & Spatial Grids**: Aggregating 2D bounding boxes in constant time.

### When NOT to Use
- **Frequently Mutated Data**: If values in the underlying array change often, updating prefix sums requires $O(N)$ recomputation. Use a Fenwick Tree (Binary Indexed Tree) or Segment Tree ($O(\log N)$ updates and queries) for dynamic arrays.

---

## 4. Implementations & Quick-Copy

### PHP (8.0+)
```php
require_once __DIR__ . '/php/src/PrefixSum.php';
use DsaToolbox\PrefixSum;

$engine = new PrefixSum([50.0, 120.0, 30.0, 80.0, 200.0]);
$total = $engine->queryRange(1, 3); // 120 + 30 + 80 = 230 in O(1) time
```
👉 [Full Standalone PHP Implementation](../../php/src/PrefixSum.php)

### Python (3.9+)
```python
from prefix_sum import PrefixSum

engine = PrefixSum([50.0, 120.0, 30.0, 80.0, 200.0])
total = engine.query_range(1, 3) # 230.0
```
👉 [Full Standalone Python Implementation](../../python/src/prefix_sum.py)

### JavaScript (ES2020+)
```javascript
const { PrefixSum } = require('./PrefixSum');

const engine = new PrefixSum([50.0, 120.0, 30.0, 80.0, 200.0]);
const total = engine.queryRange(1, 3); // 230
```
👉 [Full Standalone JavaScript Implementation](../../javascript/src/PrefixSum.js)

### Java (11+)
```java
import com.dsatoolbox.PrefixSum;

PrefixSum engine = new PrefixSum(new double[]{50.0, 120.0, 30.0, 80.0, 200.0});
double total = engine.queryRange(1, 3); // 230.0
```
👉 [Full Standalone Java Implementation](../../java/src/main/java/com/dsatoolbox/PrefixSum.java)

---

## 5. Practical Enterprise Examples
- [PHP Payment Ledger Audit Engine](../../php/examples/payment_deduplication_engine.php)
- [Python Streaming Latency Interval Sum](../../python/examples/metrics_sliding_window.py)

