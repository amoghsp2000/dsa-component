# Doubly Linked List

> Bidirectional sequential data structure supporting $O(1)$ prepend, append, and arbitrary node removal given a direct reference, serving as the foundational building block for LRU caches and worker buffers.

---

## 1. Classification

- **Traditional DSA Category**: Linked Lists & Stacks and Queues
- **Real-World Engineering Use Cases**:
  - [Caching and Memory Management](../DISCOVERY_BY_USE_CASE.md#caching-and-memory-management)
  - [Queue and Background Job Processing](../DISCOVERY_BY_USE_CASE.md#queue-and-background-job-processing)

---

## 2. Complexity Analysis

| Operation | Time Complexity | Space Complexity |
|---|---|---|
| `prepend(value)` | **O(1)** | O(1) |
| `append(value)` | **O(1)** | O(1) |
| `removeNode(node)` | **O(1)** | O(1) |
| `popFirst()` | **O(1)** | O(1) |
| `popLast()` | **O(1)** | O(1) |
| **Total Memory Footprint** | - | **O(N)** |

---

## 3. Engineering Guidance

### When to Use
- **Custom Eviction Chains**: Constructing LRU, MRU, or LFU caches where arbitrary interior nodes must be detached and spliced to the head in $O(1)$ time.
- **Double-Ended Queues (Deques)**: Buffering operations requiring fast push and pop at both boundaries without array reallocation penalties.

### When NOT to Use
- **Random Indexed Access**: Accessing an item by index `get(i)` requires $O(N)$ linear traversal; use dynamic arrays instead.

---

## 4. Implementations & Quick-Copy

### PHP (8.0+)
```php
require_once __DIR__ . '/php/src/DoublyLinkedList.php';
use DsaToolbox\DoublyLinkedList;

$dll = new DoublyLinkedList();
$nodeA = $dll->append('Task A');
$nodeB = $dll->append('Task B');

$dll->removeNode($nodeA); // O(1) removal
```
👉 [Full Standalone PHP Implementation](../../php/src/DoublyLinkedList.php)

### Python (3.9+)
```python
from doubly_linked_list import DoublyLinkedList

dll = DoublyLinkedList[str]()
node_a = dll.append("Task A")
node_b = dll.append("Task B")

dll.remove_node(node_a) # O(1) removal
```
👉 [Full Standalone Python Implementation](../../python/src/doubly_linked_list.py)

### JavaScript (ES2020+)
```javascript
const { DoublyLinkedList } = require('./DoublyLinkedList');

const dll = new DoublyLinkedList();
const nodeA = dll.append('Task A');
const nodeB = dll.append('Task B');

dll.removeNode(nodeA); // O(1) removal
```
👉 [Full Standalone JavaScript Implementation](../../javascript/src/DoublyLinkedList.js)

### Java (11+)
```java
import com.dsatoolbox.DoublyLinkedList;

DoublyLinkedList<String> dll = new DoublyLinkedList<>();
DoublyLinkedList.Node<String> nodeA = dll.append("Task A");
dll.removeNode(nodeA); // O(1) removal
```
👉 [Full Standalone Java Implementation](../../java/src/main/java/com/dsatoolbox/DoublyLinkedList.java)

---

## 5. Practical Enterprise Examples
- [PHP High-Throughput LRU Cache Audit Log](../../php/examples/high_throughput_lru_cache.php)

