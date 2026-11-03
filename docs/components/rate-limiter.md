# Sliding Window Rate Limiter

> Precision rate limiter tracking granular timestamped request arrivals within a rolling time window using a double-ended queue, eliminating boundary bursts common in fixed-window counter limiters.

---

## 1. Classification

- **Traditional DSA Category**: Stacks and Queues (Sliding Window Deque) & Arrays and Strings
- **Real-World Engineering Use Cases**:
  - [Rate Limiting and Throttling](../DISCOVERY_BY_USE_CASE.md#rate-limiting-and-throttling)
  - [API and HTTP Processing](../DISCOVERY_BY_USE_CASE.md#api-and-http-processing)
  - [Financial and Payment Systems](../DISCOVERY_BY_USE_CASE.md#financial-and-payment-systems)

---

## 2. Complexity Analysis

| Operation | Time Complexity | Space Complexity |
|---|---|---|
| `allow(key)` | **O(K) amortized** (where K is expired entries) | O(1) |
| `getRemaining(key)` | **O(1)** | O(1) |
| `getResetTime(key)` | **O(1)** | O(1) |
| **Total Memory Footprint** | - | **O(Users × MaxRequests)** |

---

## 3. Engineering Guidance

### When to Use
- **Protecting Recharge & Payment Gateways**: Preventing rapid automated double-spending or card testing attacks.
- **Strict SLA Enforcement**: Ensuring third-party telecom API quotas are never exceeded across continuous rolling windows.
- **Preventing Boundary Reset Bursts**: Fixed-window counters allow 2x traffic spikes at minute boundaries (e.g., 100 requests at 00:59 and 100 requests at 01:00); sliding window limits traffic smoothly across any rolling interval.

### When NOT to Use
- **Ultra-High Traffic (Millions of requests/sec per key)**: Storing individual timestamps consumes memory proportional to limit capacity. For millions of requests/second, use Token Bucket or Leaky Bucket counter approximations.

---

## 4. Implementations & Quick-Copy

### PHP (8.0+)
```php
require_once __DIR__ . '/php/src/SlidingWindowRateLimiter.php';
use DsaToolbox\SlidingWindowRateLimiter;

$limiter = new SlidingWindowRateLimiter(maxRequests: 5, windowSeconds: 10.0);
if (!$limiter->allow($clientIp)) {
    http_response_code(429);
    echo "Rate limit exceeded. Try again in " . $limiter->getResetTime($clientIp) . "s";
    exit;
}
```
👉 [Full Standalone PHP Implementation](../../php/src/SlidingWindowRateLimiter.php)

### Python (3.9+)
```python
from rate_limiter import SlidingWindowRateLimiter

limiter = SlidingWindowRateLimiter(max_requests=5, window_seconds=10.0)
if not limiter.allow(client_ip):
    retry_after = limiter.get_reset_time(client_ip) - time.time()
    return {"error": "Too Many Requests", "retry_after": retry_after}, 429
```
👉 [Full Standalone Python Implementation](../../python/src/rate_limiter.py)

### JavaScript (ES2020+)
```javascript
const { SlidingWindowRateLimiter } = require('./SlidingWindowRateLimiter');

const limiter = new SlidingWindowRateLimiter(5, 10.0);
if (!limiter.allow(clientIp)) {
  res.status(429).json({ error: 'Too Many Requests' });
}
```
👉 [Full Standalone JavaScript Implementation](../../javascript/src/SlidingWindowRateLimiter.js)

### Java (11+)
```java
import com.dsatoolbox.SlidingWindowRateLimiter;

SlidingWindowRateLimiter limiter = new SlidingWindowRateLimiter(5, 10.0);
if (!limiter.allow(clientIp)) {
    // Return HTTP 429 Too Many Requests
}
```
👉 [Full Standalone Java Implementation](../../java/src/main/java/com/dsatoolbox/SlidingWindowRateLimiter.java)

---

## 5. Practical Enterprise Examples
- [PHP API Rate Limited Gateway](../../php/examples/api_rate_limited_gateway.php)
- [Express / Node.js Rate Limiting Middleware](../../javascript/examples/express_rate_limiter_middleware.js)

