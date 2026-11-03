# Binary Search & Bound Locator

> Logarithmic search algorithm over ordered collections providing exact match search, lower bound (first index $\ge$ target), upper bound (first index $>$ target), and threshold tier resolution.

---

## 1. Classification

- **Traditional DSA Category**: Searching and Sorting & Arrays and Strings
- **Real-World Engineering Use Cases**:
  - [Database Optimization](../DISCOVERY_BY_USE_CASE.md#database-optimization)
  - [Search and Filtering](../DISCOVERY_BY_USE_CASE.md#search-and-filtering)
  - [Financial and Payment Systems](../DISCOVERY_BY_USE_CASE.md#financial-and-payment-systems)

---

## 2. Complexity Analysis

| Operation | Time Complexity | Space Complexity |
|---|---|---|
| `find(arr, target)` | **O(log N)** | O(1) |
| `lowerBound(arr, target)` | **O(log N)** | O(1) |
| `upperBound(arr, target)` | **O(log N)** | O(1) |
| `findTier(sortedTiers, value)` | **O(log K)** | O(1) |
| **Total Memory Footprint** | - | **O(1)** aux space |

---

## 3. Engineering Guidance

### When to Use
- **Financial Fee & Discount Tiers**: Resolving commission brackets, tax rates, or cashback percentages based on recharge amounts in $O(\log K)$ time.
- **Log Indexing & Timestamp Slices**: Quickly finding the starting and ending indices of records within a given timestamp interval.
- **Sorted Catalog Search**: Sub-millisecond item discovery in pre-sorted data.

### When NOT to Use
- **Unsorted Data**: If data changes constantly and is unsorted, sorting costs $O(N \log N)$, which is slower than a single linear scan ($O(N)$).

---

## 4. Implementations & Quick-Copy

### PHP (8.0+)
```php
require_once __DIR__ . '/php/src/BinarySearch.php';
use DsaToolbox\BinarySearch;

$tiers = [
    ['threshold' => 10.0, 'value' => 'Bronze Top-Up'],
    ['threshold' => 50.0, 'value' => 'Silver Top-Up'],
    ['threshold' => 100.0, 'value' => 'Gold Top-Up']
];

$tier = BinarySearch::findTier($tiers, 75.0); // Returns 'Silver Top-Up'
```
👉 [Full Standalone PHP Implementation](../../php/src/BinarySearch.php)

### Python (3.9+)
```python
from binary_search import BinarySearch

tiers = [
    {"threshold": 10.0, "value": "Bronze Top-Up"},
    {"threshold": 50.0, "value": "Silver Top-Up"},
    {"threshold": 100.0, "value": "Gold Top-Up"}
]

tier = BinarySearch.find_tier(tiers, 75.0) # 'Silver Top-Up'
```
👉 [Full Standalone Python Implementation](../../python/src/binary_search.py)

### JavaScript (ES2020+)
```javascript
const { BinarySearch } = require('./BinarySearch');

const tiers = [
  { threshold: 10.0, value: 'Bronze Top-Up' },
  { threshold: 50.0, value: 'Silver Top-Up' },
  { threshold: 100.0, value: 'Gold Top-Up' }
];

const tier = BinarySearch.findTier(tiers, 75.0); // 'Silver Top-Up'
```
👉 [Full Standalone JavaScript Implementation](../../javascript/src/BinarySearch.js)

### Java (11+)
```java
import com.dsatoolbox.BinarySearch;

List<BinarySearch.Tier<String>> tiers = List.of(
    new BinarySearch.Tier<>(10.0, "Bronze Top-Up"),
    new BinarySearch.Tier<>(50.0, "Silver Top-Up"),
    new BinarySearch.Tier<>(100.0, "Gold Top-Up")
);

String tier = BinarySearch.findTier(tiers, 75.0);
```
👉 [Full Standalone Java Implementation](../../java/src/main/java/com/dsatoolbox/BinarySearch.java)

---

## 5. Practical Enterprise Examples
- [PHP Recharge Billing Pipeline with Binary Search Tiering](../../php/examples/recharge_billing_pipeline.php)
- [Java Recharge & Billing Demo](../../java/examples/RechargeBillingDemo.java)

