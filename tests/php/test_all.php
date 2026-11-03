<?php

declare(strict_types=1);

namespace DsaToolbox\Tests;

require_once __DIR__ . '/../../php/src/DoublyLinkedList.php';
require_once __DIR__ . '/../../php/src/BinarySearch.php';
require_once __DIR__ . '/../../php/src/PrefixSum.php';
require_once __DIR__ . '/../../php/src/PriorityQueue.php';
require_once __DIR__ . '/../../php/src/Trie.php';
require_once __DIR__ . '/../../php/src/UnionFind.php';
require_once __DIR__ . '/../../php/src/LRUCache.php';
require_once __DIR__ . '/../../php/src/SlidingWindowRateLimiter.php';
require_once __DIR__ . '/../../php/src/BloomFilter.php';
require_once __DIR__ . '/../../php/src/ConsistentHash.php';
require_once __DIR__ . '/../../php/src/SlidingWindow.php';
require_once __DIR__ . '/../../php/src/RetryQueue.php';

use DsaToolbox\DoublyLinkedList;
use DsaToolbox\BinarySearch;
use DsaToolbox\PrefixSum;
use DsaToolbox\PriorityQueue;
use DsaToolbox\Trie;
use DsaToolbox\UnionFind;
use DsaToolbox\LRUCache;
use DsaToolbox\SlidingWindowRateLimiter;
use DsaToolbox\BloomFilter;
use DsaToolbox\ConsistentHash;
use DsaToolbox\SlidingWindow;
use DsaToolbox\RetryQueue;

class TestRunner
{
    private int $passed = 0;
    private int $failed = 0;

    public function assert(bool $condition, string $testName): void
    {
        if ($condition) {
            $this->passed++;
            echo "  [PASS] $testName\n";
        } else {
            $this->failed++;
            echo "  [FAIL] $testName\n";
        }
    }

    public function assertEquals(mixed $expected, mixed $actual, string $testName): void
    {
        $this->assert($expected === $actual, "$testName (Expected " . json_encode($expected) . ", got " . json_encode($actual) . ")");
    }

    public function runAll(): void
    {
        echo "=== Running PHP DSA Toolbox Test Suite ===\n\n";

        $this->testDoublyLinkedList();
        $this->testBinarySearch();
        $this->testPrefixSum();
        $this->testPriorityQueue();
        $this->testTrie();
        $this->testUnionFind();
        $this->testLRUCache();
        $this->testRateLimiter();
        $this->testBloomFilter();
        $this->testConsistentHash();
        $this->testSlidingWindow();
        $this->testRetryQueue();

        echo "\n----------------------------------------\n";
        echo "Total: " . ($this->passed + $this->failed) . " | Passed: {$this->passed} | Failed: {$this->failed}\n";
        if ($this->failed > 0) {
            exit(1);
        }
    }

    private function testDoublyLinkedList(): void
    {
        echo "Testing DoublyLinkedList...\n";
        $list = new DoublyLinkedList();
        $list->append('B');
        $n1 = $list->prepend('A');
        $list->append('C');
        $this->assertEquals(3, $list->count(), "List size is 3");
        $this->assertEquals(['A', 'B', 'C'], $list->toArray(), "Order is A, B, C");

        $list->removeNode($n1);
        $this->assertEquals(['B', 'C'], $list->toArray(), "Remove head gives B, C");

        $val = $list->popLast();
        $this->assertEquals('C', $val, "PopLast returns C");
        $this->assertEquals(1, $list->count(), "Size is 1 after popLast");
    }

    private function testBinarySearch(): void
    {
        echo "Testing BinarySearch...\n";
        $arr = [10, 20, 30, 40, 50, 60];
        $this->assertEquals(2, BinarySearch::find($arr, 30), "Find exact match");
        $this->assertEquals(-1, BinarySearch::find($arr, 35), "Missing element returns -1");
        $this->assertEquals(3, BinarySearch::lowerBound($arr, 35), "Lower bound for 35 is index 3 (40)");
        $this->assertEquals(3, BinarySearch::upperBound($arr, 30), "Upper bound for 30 is index 3 (40)");

        $tiers = [
            ['threshold' => 10, 'value' => 'Bronze'],
            ['threshold' => 50, 'value' => 'Silver'],
            ['threshold' => 100, 'value' => 'Gold'],
        ];
        $this->assertEquals('Silver', BinarySearch::findTier($tiers, 75), "FindTier matches Silver for 75");
        $this->assertEquals(null, BinarySearch::findTier($tiers, 5), "FindTier returns null below lowest tier");
    }

    private function testPrefixSum(): void
    {
        echo "Testing PrefixSum...\n";
        $engine = new PrefixSum([5, 10, 15, 20, 25]);
        $this->assertEquals(45, $engine->queryRange(1, 3), "Range sum [1, 3] = 10 + 15 + 20 = 45");
        $this->assertEquals(5, $engine->queryRange(0, 0), "Single element range [0, 0] = 5");

        $matrix = [
            [1, 2, 3],
            [4, 5, 6],
            [7, 8, 9]
        ];
        $engine->build2D($matrix);
        // Subgrid from (1, 1) to (2, 2) is 5 + 6 + 8 + 9 = 28
        $this->assertEquals(28, $engine->query2D(1, 1, 2, 2), "2D subgrid query sum = 28");
    }

    private function testPriorityQueue(): void
    {
        echo "Testing PriorityQueue...\n";
        $minHeap = new PriorityQueue(true);
        $minHeap->push('Task C', 30);
        $minHeap->push('Task A', 10);
        $minHeap->push('Task B', 20);

        $this->assertEquals('Task A', $minHeap->pop(), "Min-heap pop lowest priority number first");
        $this->assertEquals('Task B', $minHeap->pop(), "Second pop is Task B");
        $this->assertEquals('Task C', $minHeap->pop(), "Third pop is Task C");

        $maxHeap = new PriorityQueue(false);
        $maxHeap->push('Task X', 10);
        $maxHeap->push('Task Z', 50);
        $this->assertEquals('Task Z', $maxHeap->pop(), "Max-heap pop highest priority number first");
    }

    private function testTrie(): void
    {
        echo "Testing Trie...\n";
        $trie = new Trie();
        $trie->insert('apple', 100);
        $trie->insert('app', 200);
        $trie->insert('application', 300);

        $this->assert($trie->search('apple'), "Trie finds exact word 'apple'");
        $this->assert(!$trie->search('appl'), "Trie correctly returns false for non-terminal prefix");
        $this->assert($trie->startsWith('app'), "Trie startsWith 'app' is true");
        $this->assertEquals(200, $trie->getValue('app'), "Trie payload retrieval");

        $words = $trie->findWordsWithPrefix('app');
        $this->assertEquals(3, count($words), "Finds all 3 words with prefix 'app'");

        $deleted = $trie->delete('app');
        $this->assert($deleted, "Delete returns true");
        $this->assert(!$trie->search('app'), "'app' no longer exists");
        $this->assert($trie->search('apple'), "'apple' remains unaffected after 'app' deletion");
    }

    private function testUnionFind(): void
    {
        echo "Testing UnionFind...\n";
        $uf = new UnionFind();
        $this->assert($uf->union('A', 'B'), "Union A and B succeeds");
        $this->assert($uf->union('B', 'C'), "Union B and C succeeds");
        $this->assert($uf->connected('A', 'C'), "A and C are connected transitively");
        $this->assertEquals(3, $uf->getComponentSize('A'), "Component size is 3");
        $this->assert(!$uf->union('A', 'C'), "Unioning already connected elements returns false (cycle detected)");
    }

    private function testLRUCache(): void
    {
        echo "Testing LRUCache...\n";
        $cache = new LRUCache(2);
        $cache->put('k1', 'v1');
        $cache->put('k2', 'v2');
        $this->assertEquals('v1', $cache->get('k1'), "Retrieve k1 (makes k1 MRU, k2 LRU)");

        $cache->put('k3', 'v3'); // Should evict k2
        $this->assertEquals(null, $cache->get('k2'), "k2 was evicted");
        $this->assertEquals('v1', $cache->get('k1'), "k1 remains");
        $this->assertEquals('v3', $cache->get('k3'), "k3 exists");
        $this->assertEquals(1, $cache->getEvictionCount(), "Eviction count is 1");
    }

    private function testRateLimiter(): void
    {
        echo "Testing SlidingWindowRateLimiter...\n";
        $limiter = new SlidingWindowRateLimiter(2, 5.0);
        $now = 1000.0;
        $this->assert($limiter->allow('client1', $now), "First request allowed");
        $this->assert($limiter->allow('client1', $now + 1.0), "Second request allowed");
        $this->assert(!$limiter->allow('client1', $now + 2.0), "Third request rejected (limit 2 exceeded)");
        $this->assertEquals(0, $limiter->getRemaining('client1', $now + 2.0), "0 remaining requests");

        // After window slides past 5.0 seconds
        $this->assert($limiter->allow('client1', $now + 6.0), "Allowed after sliding window rolls over");
    }

    private function testBloomFilter(): void
    {
        echo "Testing BloomFilter...\n";
        $filter = new BloomFilter(100, 0.01);
        $filter->add("tx_abc_123");
        $filter->add("tx_def_456");

        $this->assert($filter->contains("tx_abc_123"), "Contains added item 1");
        $this->assert($filter->contains("tx_def_456"), "Contains added item 2");
        $this->assert(!$filter->contains("tx_unknown_789"), "Does not contain unseen item");
    }

    private function testConsistentHash(): void
    {
        echo "Testing ConsistentHash...\n";
        $ring = new ConsistentHash(replicas: 50);
        $ring->addNode('node-alpha');
        $ring->addNode('node-beta');

        $node1 = $ring->getNode('user_key_1');
        $node2 = $ring->getNode('user_key_2');
        $this->assert(in_array($node1, ['node-alpha', 'node-beta'], true), "Key mapped to valid node");
        $this->assert(in_array($node2, ['node-alpha', 'node-beta'], true), "Key mapped to valid node");
    }

    private function testSlidingWindow(): void
    {
        echo "Testing SlidingWindow Monotonic Deque...\n";
        $nums = [1, 3, -1, -3, 5, 3, 6, 7];
        $k = 3;
        $slidingMax = SlidingWindow::computeSlidingMax($nums, $k);
        // Expected sliding window max for k=3: [3, 3, 5, 5, 6, 7]
        $this->assertEquals([3, 3, 5, 5, 6, 7], $slidingMax, "Sliding max computation matches LeetCode benchmark");
    }

    private function testRetryQueue(): void
    {
        echo "Testing RetryQueue...\n";
        $retry = new RetryQueue(baseBackoff: 0.1, maxBackoff: 2.0);
        $retry->enqueue('task_pay_1', ['amount' => 50], maxAttempts: 2, delaySeconds: 0.0);
        $this->assertEquals(1, $retry->count(), "Task active in queue");

        $ready = $retry->pollReady();
        $this->assertEquals(1, count($ready), "Immediate task is polled ready");

        // Retry 1: Should stay active
        $requeued = $retry->retry('task_pay_1', 'Gateway timeout');
        $this->assert($requeued, "Task re-enqueued for backoff");

        // Retry 2: Should hit maxAttempts (2) and route to DLQ
        $requeuedSecond = $retry->retry('task_pay_1', 'Permanent timeout');
        $this->assert(!$requeuedSecond, "Task exhausted attempts and moved to DLQ");
        $this->assertEquals(1, $retry->dlqCount(), "DLQ has 1 item");
    }
}

$runner = new TestRunner();
$runner->runAll();

