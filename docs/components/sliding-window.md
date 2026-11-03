# Monotonic Queue (Sliding Window Extremes)

> Double-ended monotonic queue maintaining elements in decreasing or increasing order to compute sliding window minimums, maximums, and rolling telemetry metrics in $O(1)$ amortized time.

---

## 1. Classification

- **Traditional DSA Category**: Stacks and Queues (Monotonic Deque) & Arrays and Strings
- **Real-World Engineering Use Cases**:
  - [Real-Time Data Processing](../DISCOVERY_BY_USE_CASE.md#real-time-data-processing)
  - [Logging and Monitoring](../DISCOVERY_BY_USE_CASE.md#logging-and-monitoring)
  - [Financial and Payment Systems](../DISCOVERY_BY_USE_CASE.md#financial-and-payment-systems)
  - [Performance Optimization](../DISCOVERY_BY_USE_CASE.md#performance-optimization)

---

## 2. Complexity Analysis

| Operation | Time Complexity | Space Complexity |
|---|---|---|
| `push(val)` | **O(1) amortized** | O(1) |
| `pop()` | **O(1)** | O(1) |
| `getMax()` | **O(1)** | O(1) |
| `computeSlidingMax(nums, k)` | **O(N) total** | O(K) |
| **Total Memory Footprint** | - | **O(K)** (where K is window size) |

---

## 3. Engineering Guidance

### When to Use
- **Rolling Peak Latency Tracking**: Computing the 99th percentile or absolute peak latency across a 5-minute sliding window of telemetry.
- **Financial Fraud Spikes**: Detecting anomalous transaction volume surges or drawdown bursts over sliding time frames.
- **Algorithmic Trading**: Computing moving Bollinger bounds or sliding channel breakouts in real-time streaming engines.

### When NOT to Use
- **Dynamic Element Removal by ID**: Monotonic queues operate on sequential stream order (FIFO); removing an arbitrary interior element by key is not supported.

---

## 4. Implementations & Quick-Copy

### PHP (8.0+)
```php
require_once __DIR__ . '/php/src/SlidingWindow.php';
use DsaToolbox\SlidingWindow;

$nums = [120, 145, 98, 350, 420, 110, 95];
$peakLatencies = SlidingWindow::computeSlidingMax($nums, k: 3);
// Returns sliding maximums for each 3-element interval
```
👉 [Full Standalone PHP Implementation](../../php/src/SlidingWindow.php)

### Python (3.9+)
```python
from sliding_window import SlidingWindow

nums = [120, 145, 98, 350, 420, 110, 95]
peak_latencies = SlidingWindow.compute_sliding_max(nums, k=3)
```
👉 [Full Standalone Python Implementation](../../python/src/sliding_window.py)

### JavaScript (ES2020+)
```javascript
const { SlidingWindow } = require('./SlidingWindow');

const nums = [120, 145, 98, 350, 420, 110, 95];
const peakLatencies = SlidingWindow.computeSlidingMax(nums, 3);
```
👉 [Full Standalone JavaScript Implementation](../../javascript/src/SlidingWindow.js)

### Java (11+)
```java
import com.dsatoolbox.SlidingWindow;

double[] nums = {120, 145, 98, 350, 420, 110, 95};
double[] peakLatencies = SlidingWindow.computeSlidingMax(nums, 3);
```
👉 [Full Standalone Java Implementation](../../java/src/main/java/com/dsatoolbox/SlidingWindow.java)

---

## 5. Practical Enterprise Examples
- [Python Streaming Telemetry Metrics with Monotonic Queue](../../python/examples/metrics_sliding_window.py)

