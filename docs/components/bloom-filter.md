# Bloom Filter

> Ultra space-efficient probabilistic bit array utilizing independent hash functions to test set membership with **zero false negatives** and mathematically tunable false positive probability.

---

## 1. Classification

- **Traditional DSA Category**: Advanced Data Structures & Bit Manipulation
- **Real-World Engineering Use Cases**:
  - [Data Deduplication and Validation](../DISCOVERY_BY_USE_CASE.md#data-deduplication-and-validation)
  - [Database Optimization](../DISCOVERY_BY_USE_CASE.md#database-optimization)
  - [Financial and Payment Systems](../DISCOVERY_BY_USE_CASE.md#financial-and-payment-systems)
  - [Caching and Memory Management](../DISCOVERY_BY_USE_CASE.md#caching-and-memory-management)

---

## 2. Complexity Analysis

| Operation | Time Complexity | Space Complexity |
|---|---|---|
| `add(item)` | **O(K)** (where K is number of hash iterations) | O(1) |
| `contains(item)` | **O(K)** | O(1) |
| `clear()` | **O(M / 64)** | O(1) |
| **Total Memory Footprint** | - | **O(M bits)** (~10 bits/item for 1% error rate) |

---

## 3. Engineering Guidance

### When to Use
- **Idempotency Key Pre-Screening**: Check if an incoming transaction reference or webhook UUID has ever been seen before executing expensive database writes.
- **Cache Penetration Protection**: If a requested user ID or product slug does not exist, the Bloom Filter answers `false` immediately, preventing cold queries from crashing backend databases.
- **Large-Scale Deduplication**: Tracking billions of URLs, event IDs, or logs in a few megabytes of RAM.

### When NOT to Use
- **Zero-Tolerance for False Positives**: While false negatives are mathematically impossible, false positives can occur (tunable to 0.1% or 0.01%).
- **Dynamic Deletion Required**: Standard Bloom Filters cannot delete items (turning off a bit might corrupt other items).

---

## 4. Implementations & Quick-Copy

### PHP (8.0+)
```php
require_once __DIR__ . '/php/src/BloomFilter.php';
use DsaToolbox\BloomFilter;

$bf = new BloomFilter(expectedElements: 100000, falsePositiveRate: 0.001);
$bf->add("tx_ref_990182");

if ($bf->contains("tx_ref_990182")) {
    // Possibly a duplicate -> query authoritative DB
}
```
👉 [Full Standalone PHP Implementation](../../php/src/BloomFilter.php)

### Python (3.9+)
```python
from bloom_filter import BloomFilter

bf = BloomFilter(expected_elements=100000, false_positive_rate=0.001)
bf.add("tx_ref_990182")

if bf.contains("tx_ref_990182"):
    # Possibly a duplicate -> verify with DB
    pass
```
👉 [Full Standalone Python Implementation](../../python/src/bloom_filter.py)

### JavaScript (ES2020+)
```javascript
const { BloomFilter } = require('./BloomFilter');

const bf = new BloomFilter(100000, 0.001);
bf.add('tx_ref_990182');

if (bf.contains('tx_ref_990182')) {
  // Confirm against DB
}
```
👉 [Full Standalone JavaScript Implementation](../../javascript/src/BloomFilter.js)

### Java (11+)
```java
import com.dsatoolbox.BloomFilter;

BloomFilter bf = new BloomFilter(100000, 0.001);
bf.add("tx_ref_990182");

if (bf.contains("tx_ref_990182")) {
    // Confirm against DB
}
```
👉 [Full Standalone Java Implementation](../../java/src/main/java/com/dsatoolbox/BloomFilter.java)

---

## 5. Practical Enterprise Examples
- [PHP Payment Deduplication Engine](../../php/examples/payment_deduplication_engine.php)
- [Python Payment Dedup Service](../../python/examples/payment_dedup_service.py)

