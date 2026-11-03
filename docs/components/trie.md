# Trie (Prefix Tree)

> Tree data structure where each node represents a character step, providing deterministic $O(M)$ exact search, prefix matching, and instant autocomplete suggestions where $M$ is query length.

---

## 1. Classification

- **Traditional DSA Category**: Trees and Graphs & Searching and Sorting
- **Real-World Engineering Use Cases**:
  - [Search and Filtering](../DISCOVERY_BY_USE_CASE.md#search-and-filtering)
  - [API and HTTP Processing](../DISCOVERY_BY_USE_CASE.md#api-and-http-processing)
  - [Networking and Routing](../DISCOVERY_BY_USE_CASE.md#networking-and-routing)

---

## 2. Complexity Analysis

| Operation | Time Complexity | Space Complexity |
|---|---|---|
| `insert(word, value)` | **O(M)** (where M is word length) | O(M) |
| `search(word)` | **O(M)** | O(1) |
| `startsWith(prefix)` | **O(M)** | O(1) |
| `findWordsWithPrefix(prefix)` | **O(M + K)** (where K is collected nodes) | O(K) |
| `delete(word)` | **O(M)** | O(1) |
| **Total Memory Footprint** | - | **O(AlphabetSize × M × N)** |

---

## 3. Engineering Guidance

### When to Use
- **Real-Time Autocomplete**: Instant search suggestions in user lookup, telecom billers, or catalog search as the user types.
- **API URL Route Dispatching**: Sub-millisecond matching of RESTful route prefixes (e.g., `/api/v1/billing/*`).
- **Network Routing**: Longest Prefix Match (LPM) for IP address routing and telecom phone area codes.

### When NOT to Use
- **Exact-Match-Only Without Common Prefixes**: If prefix searching is not required, a standard Hash Map provides faster and more memory-efficient lookups.

---

## 4. Implementations & Quick-Copy

### PHP (8.0+)
```php
require_once __DIR__ . '/php/src/Trie.php';
use DsaToolbox\Trie;

$trie = new Trie();
$trie->insert('/api/v1/recharge', 'RechargeController');
$trie->insert('/api/v1/billing', 'BillingController');

$suggestions = $trie->findWordsWithPrefix('/api/v1');
```
👉 [Full Standalone PHP Implementation](../../php/src/Trie.php)

### Python (3.9+)
```python
from trie import Trie

trie = Trie()
trie.insert("/api/v1/recharge", "RechargeController")
trie.insert("/api/v1/billing", "BillingController")

suggestions = trie.find_words_with_prefix("/api/v1")
```
👉 [Full Standalone Python Implementation](../../python/src/trie.py)

### JavaScript (ES2020+)
```javascript
const { Trie } = require('./Trie');

const trie = new Trie();
trie.insert('/api/v1/recharge', 'RechargeController');
trie.insert('/api/v1/billing', 'BillingController');

const suggestions = trie.findWordsWithPrefix('/api/v1');
```
👉 [Full Standalone JavaScript Implementation](../../javascript/src/Trie.js)

### Java (11+)
```java
import com.dsatoolbox.Trie;

Trie<String> trie = new Trie<>();
trie.insert("/api/v1/recharge", "RechargeController");
trie.insert("/api/v1/billing", "BillingController");

List<Trie.Entry<String>> matches = trie.findWordsWithPrefix("/api/v1", 10);
```
👉 [Full Standalone Java Implementation](../../java/src/main/java/com/dsatoolbox/Trie.java)

---

## 5. Practical Enterprise Examples
- [PHP API Rate Limited Gateway with Trie Route Dispatching](../../php/examples/api_rate_limited_gateway.php)

