# Retry Queue with Exponential Backoff & Full Jitter

> Resilient retry orchestration mechanism utilizing a min-heap priority queue and exponential backoff with full jitter to prevent thundering herd spikes during upstream outages.

---

## 1. Classification

- **Traditional DSA Category**: Stacks and Queues (Priority Min-Heap) & Greedy Algorithms
- **Real-World Engineering Use Cases**:
  - [Queue and Background Job Processing](../DISCOVERY_BY_USE_CASE.md#queue-and-background-job-processing)
  - [Financial and Payment Systems](../DISCOVERY_BY_USE_CASE.md#financial-and-payment-systems)
  - [API and HTTP Processing](../DISCOVERY_BY_USE_CASE.md#api-and-http-processing)

---

## 2. Complexity Analysis

| Operation | Time Complexity | Space Complexity |
|---|---|---|
| `enqueue(id, payload, maxAttempts)` | **O(log N)** | O(1) amortized |
| `pollReady()` | **O(log N)** per ready item, **O(1)** peek when not | O(1) |
| `retry(id, reason)` | **O(log N)** | O(1) |
| `peekNextDueTime()` | **O(1)** | O(1) |
| **Total Memory Footprint** | - | **O(N)** |

---

## 3. Engineering Guidance

### When to Use
- **Retrying Failed Payment Webhooks & Telecom Recharges**: When upstream banks or telecom gateways experience transient HTTP 500 / 503 errors.
- **Preventing Self-Inflicted DDoS (Thundering Herd)**: Standard exponential backoff causes all clients to retry in synchronized waves; randomized Full Jitter decorrelates traffic and spreads load evenly across the recovery period.
- **Dead Letter Queue (DLQ) Routing**: Safely isolating permanently poisoned or failed tasks for manual inspection after max retry attempts are reached.

### When NOT to Use
- **Non-Idempotent Operations**: Retrying a payment without an idempotency key can cause double charges! Always pair with a Bloom Filter or idempotency token.
- **Permanent Client Errors**: HTTP 400 Bad Request, 401 Unauthorized, or 404 Not Found should never be retried.

---

## 4. Implementations & Quick-Copy

### PHP (8.0+)
```php
require_once __DIR__ . '/php/src/RetryQueue.php';
use DsaToolbox\RetryQueue;

$rq = new RetryQueue(baseBackoff: 0.5, maxBackoff: 30.0);
$rq->enqueue('tx_pay_9012', ['amount' => 150.0], maxAttempts: 5);

// Poll tasks that are due right now
$readyTasks = $rq->pollReady();
foreach ($readyTasks as $task) {
    $success = dispatchToBank($task->payload);
    if (!$success) {
        $rq->retry($task->id, 'Bank gateway timeout');
    }
}
```
👉 [Full Standalone PHP Implementation](../../php/src/RetryQueue.php)

### Python (3.9+)
```python
from retry_queue import RetryQueue

rq = RetryQueue(base_backoff=0.5, max_backoff=30.0)
rq.enqueue("tx_pay_9012", {"amount": 150.0}, max_attempts=5)

for task in rq.poll_ready():
    success = dispatch_to_bank(task.payload)
    if not success:
        rq.retry(task.id, "Bank gateway timeout")
```
👉 [Full Standalone Python Implementation](../../python/src/retry_queue.py)

### JavaScript (ES2020+)
```javascript
const { RetryQueue } = require('./RetryQueue');

const rq = new RetryQueue(0.5, 30.0);
rq.enqueue('tx_pay_9012', { amount: 150.0 }, 5);

for (const task of rq.pollReady()) {
  const success = dispatchToBank(task.payload);
  if (!success) {
    rq.retry(task.id, 'Bank gateway timeout');
  }
}
```
👉 [Full Standalone JavaScript Implementation](../../javascript/src/RetryQueue.js)

### Java (11+)
```java
import com.dsatoolbox.RetryQueue;

RetryQueue<PaymentPayload> rq = new RetryQueue<>(0.5, 30.0);
rq.enqueue("tx_pay_9012", new PaymentPayload(150.0), 5, 0.0);

for (var task : rq.pollReady()) {
    boolean success = dispatchToBank(task.payload);
    if (!success) {
        rq.retry(task.id, "Bank gateway timeout");
    }
}
```
👉 [Full Standalone Java Implementation](../../java/src/main/java/com/dsatoolbox/RetryQueue.java)

---

## 5. Practical Enterprise Examples
- [PHP Telecom Recharge Pipeline with Exponential Jitter Retry](../../php/examples/recharge_billing_pipeline.php)
- [Node.js Background Job Worker with DLQ](../../javascript/examples/job_worker_priority_queue.js)

