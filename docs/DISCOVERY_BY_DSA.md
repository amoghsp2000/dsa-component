# Component Discovery by Traditional DSA Category

> Explore reusable, dependency-free DSA components categorized by theoretical Computer Science classification.

## Quick Navigation

- [Arrays and Strings](#arrays-and-strings) (5 components)
- [Hash Tables](#hash-tables) (1 component)
- [Linked Lists](#linked-lists) (2 components)
- [Stacks and Queues](#stacks-and-queues) (4 components)
- [Trees and Graphs](#trees-and-graphs) (3 components)
- [Searching and Sorting](#searching-and-sorting) (3 components)
- [Recursion and Backtracking](#recursion-and-backtracking) (0 components)
- [Greedy Algorithms](#greedy-algorithms) (1 component)
- [Dynamic Programming](#dynamic-programming) (0 components)
- [Bit Manipulation](#bit-manipulation) (1 component)
- [Advanced Data Structures](#advanced-data-structures) (3 components)

## Arrays and Strings <a id='arrays-and-strings'></a>
Contiguous memory structures and sequence algorithms for fast indexed access, slicing, windowing, and transformation.

**Key Characteristics:** O(1) random access, Cache locality, Contiguous allocation, Prefix and window operations

| Component | Difficulty | Time Complexity | Space Complexity | Real-World Use Cases | Implementations |
|---|---|---|---|---|---|
| [Sliding Window Rate Limiter](components/rate-limiter.md) | `Intermediate` | allow: `O(k) amortized, where k is expired timestamps pruned`, remaining: `O(1)` | `O(max_requests per window per key)` | Rate Limiting and Throttling, API and HTTP Processing | [PHP](../php/src/SlidingWindowRateLimiter.php) · [JS](../javascript/src/SlidingWindowRateLimiter.js) · [Python](../python/src/rate_limiter.py) · [Java](../java/src/main/java/com/dsatoolbox/SlidingWindowRateLimiter.java) |
| [Priority Queue (Min/Max Binary Heap)](components/priority-queue.md) | `Intermediate` | push: `O(log n)`, pop: `O(log n)` | `O(n)` | Queue and Background Job Processing, Scheduling and Task Management | [PHP](../php/src/PriorityQueue.php) · [JS](../javascript/src/PriorityQueue.js) · [Python](../python/src/priority_queue.py) · [Java](../java/src/main/java/com/dsatoolbox/PriorityQueue.java) |
| [Monotonic Deque (Sliding Window Extremes)](components/sliding-window.md) | `Intermediate` | push: `O(1) amortized`, pop: `O(1)` | `O(w) where w is window size` | Real-Time Data Processing, Logging and Monitoring | [PHP](../php/src/SlidingWindow.php) · [JS](../javascript/src/SlidingWindow.js) · [Python](../python/src/sliding_window.py) · [Java](../java/src/main/java/com/dsatoolbox/SlidingWindow.java) |
| [Binary Search & Bound Locator](components/binary-search.md) | `Beginner` | find: `O(log n)`, lowerBound: `O(log n)` | `O(1)` | Database Optimization, Search and Filtering | [PHP](../php/src/BinarySearch.php) · [JS](../javascript/src/BinarySearch.js) · [Python](../python/src/binary_search.py) · [Java](../java/src/main/java/com/dsatoolbox/BinarySearch.java) |
| [Prefix Sum & Range Query Engine](components/prefix-sum.md) | `Beginner` | build: `O(n)`, queryRange: `O(1)` | `O(n)` | Financial and Payment Systems, Logging and Monitoring | [PHP](../php/src/PrefixSum.php) · [JS](../javascript/src/PrefixSum.js) · [Python](../python/src/prefix_sum.py) · [Java](../java/src/main/java/com/dsatoolbox/PrefixSum.java) |

## Hash Tables <a id='hash-tables'></a>
Associative key-value structures utilizing hashing functions to provide expected amortized O(1) lookup, insertion, and deletion.

**Key Characteristics:** Expected O(1) lookup, Key hashing, Collision resolution (chaining / open addressing), Fast membership testing

| Component | Difficulty | Time Complexity | Space Complexity | Real-World Use Cases | Implementations |
|---|---|---|---|---|---|
| [LRU Cache (Least Recently Used)](components/lru-cache.md) | `Intermediate` | get: `O(1)`, put: `O(1)` | `O(capacity)` | Caching and Memory Management, API and HTTP Processing | [PHP](../php/src/LRUCache.php) · [JS](../javascript/src/LRUCache.js) · [Python](../python/src/lru_cache.py) · [Java](../java/src/main/java/com/dsatoolbox/LRUCache.java) |

## Linked Lists <a id='linked-lists'></a>
Dynamic sequential data structures where elements point to their successor and/or predecessor nodes, enabling O(1) insertion and deletion at known positions.

**Key Characteristics:** O(1) insertion/deletion at pointers, Dynamic memory growth, Non-contiguous storage, Bidirectional or unidirectional traversal

| Component | Difficulty | Time Complexity | Space Complexity | Real-World Use Cases | Implementations |
|---|---|---|---|---|---|
| [LRU Cache (Least Recently Used)](components/lru-cache.md) | `Intermediate` | get: `O(1)`, put: `O(1)` | `O(capacity)` | Caching and Memory Management, API and HTTP Processing | [PHP](../php/src/LRUCache.php) · [JS](../javascript/src/LRUCache.js) · [Python](../python/src/lru_cache.py) · [Java](../java/src/main/java/com/dsatoolbox/LRUCache.java) |
| [Doubly Linked List](components/doubly-linked-list.md) | `Beginner` | prepend: `O(1)`, append: `O(1)` | `O(n)` | Caching and Memory Management, Queue and Background Job Processing | [PHP](../php/src/DoublyLinkedList.php) · [JS](../javascript/src/DoublyLinkedList.js) · [Python](../python/src/doubly_linked_list.py) · [Java](../java/src/main/java/com/dsatoolbox/DoublyLinkedList.java) |

## Stacks and Queues <a id='stacks-and-queues'></a>
Linear abstract data types governing First-In-First-Out (FIFO) or Last-In-First-Out (LIFO) access, monotonic properties, and deque operations.

**Key Characteristics:** LIFO (Stack) / FIFO (Queue), Monotonic queues for extreme queries, Buffering and scheduling primitives

| Component | Difficulty | Time Complexity | Space Complexity | Real-World Use Cases | Implementations |
|---|---|---|---|---|---|
| [Sliding Window Rate Limiter](components/rate-limiter.md) | `Intermediate` | allow: `O(k) amortized, where k is expired timestamps pruned`, remaining: `O(1)` | `O(max_requests per window per key)` | Rate Limiting and Throttling, API and HTTP Processing | [PHP](../php/src/SlidingWindowRateLimiter.php) · [JS](../javascript/src/SlidingWindowRateLimiter.js) · [Python](../python/src/rate_limiter.py) · [Java](../java/src/main/java/com/dsatoolbox/SlidingWindowRateLimiter.java) |
| [Monotonic Deque (Sliding Window Extremes)](components/sliding-window.md) | `Intermediate` | push: `O(1) amortized`, pop: `O(1)` | `O(w) where w is window size` | Real-Time Data Processing, Logging and Monitoring | [PHP](../php/src/SlidingWindow.php) · [JS](../javascript/src/SlidingWindow.js) · [Python](../python/src/sliding_window.py) · [Java](../java/src/main/java/com/dsatoolbox/SlidingWindow.java) |
| [Doubly Linked List](components/doubly-linked-list.md) | `Beginner` | prepend: `O(1)`, append: `O(1)` | `O(n)` | Caching and Memory Management, Queue and Background Job Processing | [PHP](../php/src/DoublyLinkedList.php) · [JS](../javascript/src/DoublyLinkedList.js) · [Python](../python/src/doubly_linked_list.py) · [Java](../java/src/main/java/com/dsatoolbox/DoublyLinkedList.java) |
| [Retry Queue with Exponential Backoff & Full Jitter](components/retry-queue.md) | `Intermediate` | enqueue: `O(log n)`, pollReady: `O(log n) when items ready, O(1) peek when not` | `O(n)` | Queue and Background Job Processing, Financial and Payment Systems | [PHP](../php/src/RetryQueue.php) · [JS](../javascript/src/RetryQueue.js) · [Python](../python/src/retry_queue.py) · [Java](../java/src/main/java/com/dsatoolbox/RetryQueue.java) |

## Trees and Graphs <a id='trees-and-graphs'></a>
Hierarchical and network data structures representing nodes and edges, including Binary Search Trees, Heaps, Tries, and adjacency graphs.

**Key Characteristics:** Hierarchical relationships, Cyclic and acyclic traversal (BFS, DFS), Shortest path and spanning tree models

| Component | Difficulty | Time Complexity | Space Complexity | Real-World Use Cases | Implementations |
|---|---|---|---|---|---|
| [Priority Queue (Min/Max Binary Heap)](components/priority-queue.md) | `Intermediate` | push: `O(log n)`, pop: `O(log n)` | `O(n)` | Queue and Background Job Processing, Scheduling and Task Management | [PHP](../php/src/PriorityQueue.php) · [JS](../javascript/src/PriorityQueue.js) · [Python](../python/src/priority_queue.py) · [Java](../java/src/main/java/com/dsatoolbox/PriorityQueue.java) |
| [Trie (Prefix Tree)](components/trie.md) | `Intermediate` | insert: `O(m)`, search: `O(m)` | `O(ALPHABET_SIZE * m * n)` | Search and Filtering, API and HTTP Processing | [PHP](../php/src/Trie.php) · [JS](../javascript/src/Trie.js) · [Python](../python/src/trie.py) · [Java](../java/src/main/java/com/dsatoolbox/Trie.java) |
| [Disjoint Set Union (Union-Find)](components/union-find.md) | `Intermediate` | find: `O(alpha(n)) near O(1)`, union: `O(alpha(n)) near O(1)` | `O(n)` | Networking and Routing, Distributed Systems | [PHP](../php/src/UnionFind.php) · [JS](../javascript/src/UnionFind.js) · [Python](../python/src/union_find.py) · [Java](../java/src/main/java/com/dsatoolbox/UnionFind.java) |

## Searching and Sorting <a id='searching-and-sorting'></a>
Algorithms designed to locate elements in ordered and unordered data collections, reorder sequences, and partition spaces efficiently.

**Key Characteristics:** Divide and conquer, Logarithmic search boundaries, Comparison-based and non-comparison sorting

| Component | Difficulty | Time Complexity | Space Complexity | Real-World Use Cases | Implementations |
|---|---|---|---|---|---|
| [Trie (Prefix Tree)](components/trie.md) | `Intermediate` | insert: `O(m)`, search: `O(m)` | `O(ALPHABET_SIZE * m * n)` | Search and Filtering, API and HTTP Processing | [PHP](../php/src/Trie.php) · [JS](../javascript/src/Trie.js) · [Python](../python/src/trie.py) · [Java](../java/src/main/java/com/dsatoolbox/Trie.java) |
| [Consistent Hash Ring](components/consistent-hashing.md) | `Advanced` | addNode: `O(v * log(N * v)) where v is virtual nodes and N is server count`, removeNode: `O(v * log(N * v))` | `O(N * v)` | Distributed Systems, Caching and Memory Management | [PHP](../php/src/ConsistentHash.php) · [JS](../javascript/src/ConsistentHash.js) · [Python](../python/src/consistent_hash.py) · [Java](../java/src/main/java/com/dsatoolbox/ConsistentHash.java) |
| [Binary Search & Bound Locator](components/binary-search.md) | `Beginner` | find: `O(log n)`, lowerBound: `O(log n)` | `O(1)` | Database Optimization, Search and Filtering | [PHP](../php/src/BinarySearch.php) · [JS](../javascript/src/BinarySearch.js) · [Python](../python/src/binary_search.py) · [Java](../java/src/main/java/com/dsatoolbox/BinarySearch.java) |

## Recursion and Backtracking <a id='recursion-and-backtracking'></a>
Algorithmic strategies solving problems by decomposing into sub-problems and systematically exploring state spaces with pruning.

**Key Characteristics:** Call stack state persistence, State space tree exploration, Constraint satisfaction and pruning

*No components currently registered in this category.*

## Greedy Algorithms <a id='greedy-algorithms'></a>
Optimization paradigms making locally optimal choices at each stage with the expectation of finding a global optimum.

**Key Characteristics:** Optimal substructure, Greedy choice property, High runtime efficiency

| Component | Difficulty | Time Complexity | Space Complexity | Real-World Use Cases | Implementations |
|---|---|---|---|---|---|
| [Retry Queue with Exponential Backoff & Full Jitter](components/retry-queue.md) | `Intermediate` | enqueue: `O(log n)`, pollReady: `O(log n) when items ready, O(1) peek when not` | `O(n)` | Queue and Background Job Processing, Financial and Payment Systems | [PHP](../php/src/RetryQueue.php) · [JS](../javascript/src/RetryQueue.js) · [Python](../python/src/retry_queue.py) · [Java](../java/src/main/java/com/dsatoolbox/RetryQueue.java) |

## Dynamic Programming <a id='dynamic-programming'></a>
Algorithmic technique solving complex problems by breaking them into overlapping subproblems and storing intermediate solutions (memoization or tabulation).

**Key Characteristics:** Overlapping subproblems, Optimal substructure, Memoization / Bottom-up tabulation

*No components currently registered in this category.*

## Bit Manipulation <a id='bit-manipulation'></a>
Low-level bitwise operations operating directly on binary representations of integers for ultra-fast, zero-overhead computation.

**Key Characteristics:** Bit masks, Bit shifting, Compact space representation, Hardware-accelerated speed

| Component | Difficulty | Time Complexity | Space Complexity | Real-World Use Cases | Implementations |
|---|---|---|---|---|---|
| [Bloom Filter](components/bloom-filter.md) | `Advanced` | add: `O(k), where k is number of hash functions`, contains: `O(k)` | `O(m) bits` | Data Deduplication and Validation, Database Optimization | [PHP](../php/src/BloomFilter.php) · [JS](../javascript/src/BloomFilter.js) · [Python](../python/src/bloom_filter.py) · [Java](../java/src/main/java/com/dsatoolbox/BloomFilter.java) |

## Advanced Data Structures <a id='advanced-data-structures'></a>
Specialized data structures combining multiple primitives to solve domain-specific problems, such as Disjoint-Set Union, Bloom Filters, and Consistent Hash Rings.

**Key Characteristics:** Hybrid compositions, Near constant-time inverse Ackermann complexity, Probabilistic space savings, Distributed node partitioning

| Component | Difficulty | Time Complexity | Space Complexity | Real-World Use Cases | Implementations |
|---|---|---|---|---|---|
| [Bloom Filter](components/bloom-filter.md) | `Advanced` | add: `O(k), where k is number of hash functions`, contains: `O(k)` | `O(m) bits` | Data Deduplication and Validation, Database Optimization | [PHP](../php/src/BloomFilter.php) · [JS](../javascript/src/BloomFilter.js) · [Python](../python/src/bloom_filter.py) · [Java](../java/src/main/java/com/dsatoolbox/BloomFilter.java) |
| [Consistent Hash Ring](components/consistent-hashing.md) | `Advanced` | addNode: `O(v * log(N * v)) where v is virtual nodes and N is server count`, removeNode: `O(v * log(N * v))` | `O(N * v)` | Distributed Systems, Caching and Memory Management | [PHP](../php/src/ConsistentHash.php) · [JS](../javascript/src/ConsistentHash.js) · [Python](../python/src/consistent_hash.py) · [Java](../java/src/main/java/com/dsatoolbox/ConsistentHash.java) |
| [Disjoint Set Union (Union-Find)](components/union-find.md) | `Intermediate` | find: `O(alpha(n)) near O(1)`, union: `O(alpha(n)) near O(1)` | `O(n)` | Networking and Routing, Distributed Systems | [PHP](../php/src/UnionFind.php) · [JS](../javascript/src/UnionFind.js) · [Python](../python/src/union_find.py) · [Java](../java/src/main/java/com/dsatoolbox/UnionFind.java) |

