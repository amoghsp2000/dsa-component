# Priority Queue (Min / Max Binary Heap)

> Array-backed complete binary tree guaranteeing constant-time inspection of the extreme element and logarithmic insertion and extraction.

---

## 1. Classification

- **Traditional DSA Category**: Trees and Graphs (Binary Heap) & Arrays and Strings
- **Real-World Engineering Use Cases**:
  - [Queue and Background Job Processing](../DISCOVERY_BY_USE_CASE.md#queue-and-background-job-processing)
  - [Scheduling and Task Management](../DISCOVERY_BY_USE_CASE.md#scheduling-and-task-management)
  - [Real-Time Data Processing](../DISCOVERY_BY_USE_CASE.md#real-time-data-processing)

---

## 2. Complexity Analysis

| Operation | Time Complexity | Space Complexity |
|---|---|---|
| `push(value, priority)` | **O(log N)** | O(1) amortized |
| `pop()` | **O(log N)** | O(1) |
| `peek()` | **O(1)** | O(1) |
| `count()` | **O(1)** | O(1) |
| **Total Memory Footprint** | - | **O(N)** |

---

## 3. Engineering Guidance

### When to Use
- **SLA-Driven Background Workers**: Dispatching critical operations (emergency recharge, VIP refunds, fraud alerts) ahead of bulk marketing emails.
- **Task Scheduling**: Ordering jobs by earliest execution timestamp ($O(1)$ to check if any task is due).
- **Streaming Top-K & Shortest Paths**: Finding top items in streaming data or implementing Dijkstra's algorithm.

### When NOT to Use
- **Strict FIFO Queues**: If order of arrival must be preserved without priority scores, use a standard Queue or Doubly Linked List.
- **Frequent Random Value Search**: Locating arbitrary values inside a binary heap is $O(N)$ linear scan.

---

## 4. Implementations & Quick-Copy

### PHP (8.0+)
```php
require_once __DIR__ . '/php/src/PriorityQueue.php';
use DsaToolbox\PriorityQueue;

// Max-heap: highest numerical priority processed first
$pq = new PriorityQueue(isMinHeap: false);
$pq->push('Standard Prepaid Recharge', 100);
$pq->push('Emergency Utility Bill Pay', 1500);

echo $pq->pop(); // Outputs: 'Emergency Utility Bill Pay'
```
👉 [Full Standalone PHP Implementation](../../php/src/PriorityQueue.php)

### Python (3.9+)
```python
from priority_queue import PriorityQueue

pq = PriorityQueue[str](is_min_heap=False)
pq.push("Standard Prepaid Recharge", 100)
pq.push("Emergency Utility Bill Pay", 1500)

print(pq.pop()) # Outputs: 'Emergency Utility Bill Pay'
```
👉 [Full Standalone Python Implementation](../../python/src/priority_queue.py)

### JavaScript (ES2020+)
```javascript
const { PriorityQueue } = require('./PriorityQueue');

const pq = new PriorityQueue(false);
pq.push('Standard Prepaid Recharge', 100);
pq.push('Emergency Utility Bill Pay', 1500);

console.log(pq.pop()); // 'Emergency Utility Bill Pay'
```
👉 [Full Standalone JavaScript Implementation](../../javascript/src/PriorityQueue.js)

### Java (11+)
```java
import com.dsatoolbox.PriorityQueue;

PriorityQueue<String> pq = new PriorityQueue<>(false);
pq.push("Standard Prepaid Recharge", 100.0);
pq.push("Emergency Utility Bill Pay", 1500.0);

System.out.println(pq.pop()); // "Emergency Utility Bill Pay"
```
👉 [Full Standalone Java Implementation](../../java/src/main/java/com/dsatoolbox/PriorityQueue.java)

---

## 5. Practical Enterprise Examples
- [PHP Recharge Billing Pipeline](../../php/examples/recharge_billing_pipeline.php)
- [Node.js Background Job Worker](../../javascript/examples/job_worker_priority_queue.js)

