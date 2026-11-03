/**
 * Comprehensive JavaScript Test Suite for DSA Toolbox
 * Runs in Node.js or browser without any external test dependencies.
 */

const { DoublyLinkedList } = require('../../javascript/src/DoublyLinkedList');
const { BinarySearch } = require('../../javascript/src/BinarySearch');
const { PrefixSum } = require('../../javascript/src/PrefixSum');
const { PriorityQueue } = require('../../javascript/src/PriorityQueue');
const { Trie } = require('../../javascript/src/Trie');
const { UnionFind } = require('../../javascript/src/UnionFind');
const { LRUCache } = require('../../javascript/src/LRUCache');
const { SlidingWindowRateLimiter } = require('../../javascript/src/SlidingWindowRateLimiter');
const { BloomFilter } = require('../../javascript/src/BloomFilter');
const { ConsistentHash } = require('../../javascript/src/ConsistentHash');
const { SlidingWindow } = require('../../javascript/src/SlidingWindow');
const { RetryQueue } = require('../../javascript/src/RetryQueue');

class JSTestRunner {
  constructor() {
    this.passed = 0;
    this.failed = 0;
  }

  assert(condition, message) {
    if (condition) {
      this.passed++;
      console.log(`  [PASS] ${message}`);
    } else {
      this.failed++;
      console.error(`  [FAIL] ${message}`);
    }
  }

  assertEquals(expected, actual, message) {
    const isEq = JSON.stringify(expected) === JSON.stringify(actual);
    this.assert(isEq, `${message} (Expected ${JSON.stringify(expected)}, got ${JSON.stringify(actual)})`);
  }

  runAll() {
    console.log('=== Running JavaScript DSA Toolbox Test Suite ===\n');

    this.testDoublyLinkedList();
    this.testBinarySearch();
    this.testPrefixSum();
    this.testPriorityQueue();
    this.testTrie();
    this.testUnionFind();
    this.testLRUCache();
    this.testRateLimiter();
    this.testBloomFilter();
    this.testConsistentHash();
    this.testSlidingWindow();
    this.testRetryQueue();

    console.log('\n----------------------------------------');
    console.log(`Total: ${this.passed + this.failed} | Passed: ${this.passed} | Failed: ${this.failed}`);
    if (this.failed > 0 && typeof process !== 'undefined') {
      process.exit(1);
    }
  }

  testDoublyLinkedList() {
    console.log('Testing DoublyLinkedList...');
    const dll = new DoublyLinkedList();
    dll.append('B');
    const n1 = dll.prepend('A');
    dll.append('C');
    this.assertEquals(3, dll.length, 'Size is 3');
    this.assertEquals(['A', 'B', 'C'], dll.toArray(), 'Array is A, B, C');

    dll.removeNode(n1);
    this.assertEquals(['B', 'C'], dll.toArray(), 'After remove head, gives B, C');
    this.assertEquals('C', dll.popLast(), 'PopLast returns C');
  }

  testBinarySearch() {
    console.log('Testing BinarySearch...');
    const arr = [10, 20, 30, 40, 50, 60];
    this.assertEquals(2, BinarySearch.find(arr, 30), 'Find exact match at index 2');
    this.assertEquals(-1, BinarySearch.find(arr, 35), 'Missing element returns -1');
    this.assertEquals(3, BinarySearch.lowerBound(arr, 35), 'Lower bound for 35 is index 3');
    this.assertEquals(3, BinarySearch.upperBound(arr, 30), 'Upper bound for 30 is index 3');

    const tiers = [
      { threshold: 10, value: 'Bronze' },
      { threshold: 50, value: 'Silver' },
      { threshold: 100, value: 'Gold' },
    ];
    this.assertEquals('Silver', BinarySearch.findTier(tiers, 75), 'Find tier matches Silver');
    this.assertEquals(null, BinarySearch.findTier(tiers, 5), 'Below lowest tier returns null');
  }

  testPrefixSum() {
    console.log('Testing PrefixSum...');
    const ps = new PrefixSum([5, 10, 15, 20, 25]);
    this.assertEquals(45, ps.queryRange(1, 3), 'Range sum [1, 3] = 45');
    this.assertEquals(5, ps.queryRange(0, 0), 'Single element range [0, 0] = 5');

    const matrix = [
      [1, 2, 3],
      [4, 5, 6],
      [7, 8, 9]
    ];
    ps.build2D(matrix);
    this.assertEquals(28, ps.query2D(1, 1, 2, 2), '2D subgrid query sum = 28');
  }

  testPriorityQueue() {
    console.log('Testing PriorityQueue...');
    const minHeap = new PriorityQueue(true);
    minHeap.push('Task C', 30);
    minHeap.push('Task A', 10);
    minHeap.push('Task B', 20);

    this.assertEquals('Task A', minHeap.pop(), 'Min heap pop lowest priority number');
    this.assertEquals('Task B', minHeap.pop(), 'Second pop is Task B');

    const maxHeap = new PriorityQueue(false);
    maxHeap.push('Job 1', 10);
    maxHeap.push('Job 2', 90);
    this.assertEquals('Job 2', maxHeap.pop(), 'Max heap pop highest priority number');
  }

  testTrie() {
    console.log('Testing Trie...');
    const trie = new Trie();
    trie.insert('apple', 100);
    trie.insert('app', 200);
    trie.insert('application', 300);

    this.assert(trie.search('apple'), 'Trie finds exact word apple');
    this.assert(!trie.search('appl'), 'Trie does not find incomplete prefix as word');
    this.assert(trie.startsWith('app'), 'StartsWith app is true');
    this.assertEquals(200, trie.getValue('app'), 'Payload retrieval');

    const words = trie.findWordsWithPrefix('app');
    this.assertEquals(3, words.length, 'Finds all 3 prefix matches');

    this.assert(trie.delete('app'), 'Delete app returns true');
    this.assert(!trie.search('app'), 'app deleted');
    this.assert(trie.search('apple'), 'apple still present');
  }

  testUnionFind() {
    console.log('Testing UnionFind...');
    const uf = new UnionFind();
    this.assert(uf.union('A', 'B'), 'Union A and B');
    this.assert(uf.union('B', 'C'), 'Union B and C');
    this.assert(uf.connected('A', 'C'), 'A and C are connected');
    this.assertEquals(3, uf.getComponentSize('A'), 'Component size is 3');
    this.assert(!uf.union('A', 'C'), 'Cycle detected on existing edge');
  }

  testLRUCache() {
    console.log('Testing LRUCache...');
    const cache = new LRUCache(2);
    cache.put('k1', 'v1');
    cache.put('k2', 'v2');
    this.assertEquals('v1', cache.get('k1'), 'Get k1 makes it MRU');

    cache.put('k3', 'v3'); // Evicts k2
    this.assertEquals(null, cache.get('k2'), 'k2 was evicted');
    this.assertEquals('v1', cache.get('k1'), 'k1 exists');
    this.assertEquals('v3', cache.get('k3'), 'k3 exists');
    this.assertEquals(1, cache.evictionCount, 'Eviction count is 1');
  }

  testRateLimiter() {
    console.log('Testing SlidingWindowRateLimiter...');
    const limiter = new SlidingWindowRateLimiter(2, 5.0);
    const now = 1000.0;
    this.assert(limiter.allow('client1', now), 'Request 1 allowed');
    this.assert(limiter.allow('client1', now + 1.0), 'Request 2 allowed');
    this.assert(!limiter.allow('client1', now + 2.0), 'Request 3 blocked');
    this.assertEquals(0, limiter.getRemaining('client1', now + 2.0), '0 remaining');

    // After window slides past 5 seconds
    this.assert(limiter.allow('client1', now + 6.0), 'Request allowed after window slides');
  }

  testBloomFilter() {
    console.log('Testing BloomFilter...');
    const bf = new BloomFilter(100, 0.01);
    bf.add('order_101');
    bf.add('order_102');
    this.assert(bf.contains('order_101'), 'Contains order_101');
    this.assert(bf.contains('order_102'), 'Contains order_102');
    this.assert(!bf.contains('order_999'), 'Does not contain unseen order');
  }

  testConsistentHash() {
    console.log('Testing ConsistentHash...');
    const ch = new ConsistentHash(50);
    ch.addNode('node-1');
    ch.addNode('node-2');
    const n = ch.getNode('session_key_445');
    this.assert(['node-1', 'node-2'].includes(n), 'Mapped to valid node');
  }

  testSlidingWindow() {
    console.log('Testing SlidingWindow Monotonic Deque...');
    const nums = [1, 3, -1, -3, 5, 3, 6, 7];
    const maxs = SlidingWindow.computeSlidingMax(nums, 3);
    this.assertEquals([3, 3, 5, 5, 6, 7], maxs, 'Sliding window max matches benchmark');
  }

  testRetryQueue() {
    console.log('Testing RetryQueue...');
    const rq = new RetryQueue(0.1, 1.0);
    rq.enqueue('job1', { data: 'test' }, 2, 0.0);
    this.assertEquals(1, rq.size, 'Queue size is 1');

    const ready = rq.pollReady();
    this.assertEquals(1, ready.length, 'Job ready immediately');

    this.assert(rq.retry('job1', 'Fail 1'), 'Re-enqueued for retry');
    this.assert(!rq.retry('job1', 'Fail 2'), 'Exhausted attempts moves to DLQ');
    this.assertEquals(1, rq.dlqSize, 'DLQ size is 1');
  }
}

const runner = new JSTestRunner();
runner.runAll();

