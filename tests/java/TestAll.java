package com.dsatoolbox.tests;

import com.dsatoolbox.*;
import java.util.Arrays;
import java.util.List;

public class TestAll {

    private static int passed = 0;
    private static int failed = 0;

    private static void assertTrue(boolean condition, String message) {
        if (condition) {
            passed++;
            System.out.println("  [PASS] " + message);
        } else {
            failed++;
            System.err.println("  [FAIL] " + message);
        }
    }

    private static void assertEquals(Object expected, Object actual, String message) {
        boolean eq = (expected == null && actual == null) || (expected != null && expected.equals(actual));
        assertTrue(eq, message + " (Expected " + expected + ", got " + actual + ")");
    }

    public static void main(String[] args) {
        System.out.println("=== Running Java DSA Toolbox Test Suite ===\n");

        testDoublyLinkedList();
        testBinarySearch();
        testPrefixSum();
        testPriorityQueue();
        testTrie();
        testUnionFind();
        testLRUCache();
        testRateLimiter();
        testBloomFilter();
        testConsistentHash();
        testSlidingWindow();
        testRetryQueue();

        System.out.println("\n----------------------------------------");
        System.out.println("Total: " + (passed + failed) + " | Passed: " + passed + " | Failed: " + failed);
        if (failed > 0) {
            System.exit(1);
        }
    }

    private static void testDoublyLinkedList() {
        System.out.println("Testing DoublyLinkedList...");
        DoublyLinkedList<String> list = new DoublyLinkedList<>();
        list.append("B");
        DoublyLinkedList.Node<String> n1 = list.prepend("A");
        list.append("C");

        assertEquals(3, list.size(), "Size is 3");
        assertEquals(Arrays.asList("A", "B", "C"), list.toList(), "Elements are A, B, C");

        list.removeNode(n1);
        assertEquals(Arrays.asList("B", "C"), list.toList(), "After remove head");
        assertEquals("C", list.popLast(), "Pop last is C");
    }

    private static void testBinarySearch() {
        System.out.println("Testing BinarySearch...");
        List<Integer> list = Arrays.asList(10, 20, 30, 40, 50, 60);
        assertEquals(2, BinarySearch.find(list, 30), "Find exact match");
        assertEquals(-1, BinarySearch.find(list, 35), "Missing element returns -1");
        assertEquals(3, BinarySearch.lowerBound(list, 35), "Lower bound 35 is index 3");
        assertEquals(3, BinarySearch.upperBound(list, 30), "Upper bound 30 is index 3");
    }

    private static void testPrefixSum() {
        System.out.println("Testing PrefixSum...");
        PrefixSum ps = new PrefixSum(new double[]{5, 10, 15, 20, 25});
        assertEquals(45.0, ps.queryRange(1, 3), "Range sum [1, 3] = 45");
        assertEquals(5.0, ps.queryRange(0, 0), "Single element range [0, 0] = 5");
    }

    private static void testPriorityQueue() {
        System.out.println("Testing PriorityQueue...");
        PriorityQueue<String> minHeap = new PriorityQueue<>(true);
        minHeap.push("Task C", 30.0);
        minHeap.push("Task A", 10.0);
        minHeap.push("Task B", 20.0);

        assertEquals("Task A", minHeap.pop(), "Min-heap extracts Task A first");
        assertEquals("Task B", minHeap.pop(), "Second pop is Task B");

        PriorityQueue<String> maxHeap = new PriorityQueue<>(false);
        maxHeap.push("Job 1", 10.0);
        maxHeap.push("Job 2", 90.0);
        assertEquals("Job 2", maxHeap.pop(), "Max-heap extracts Job 2 first");
    }

    private static void testTrie() {
        System.out.println("Testing Trie...");
        Trie<Integer> trie = new Trie<>();
        trie.insert("apple", 100);
        trie.insert("app", 200);

        assertTrue(trie.search("apple"), "Finds apple");
        assertTrue(!trie.search("appl"), "Does not match prefix as word");
        assertTrue(trie.startsWith("app"), "StartsWith app is true");
        assertEquals(200, trie.getValue("app"), "Retrieves app payload");

        assertTrue(trie.delete("app"), "Deletes app");
        assertTrue(!trie.search("app"), "app no longer exists");
        assertTrue(trie.search("apple"), "apple remains intact");
    }

    private static void testUnionFind() {
        System.out.println("Testing UnionFind...");
        UnionFind uf = new UnionFind();
        assertTrue(uf.union("A", "B"), "Union A and B");
        assertTrue(uf.union("B", "C"), "Union B and C");
        assertTrue(uf.connected("A", "C"), "A and C are connected");
        assertEquals(3, uf.getComponentSize("A"), "Component size is 3");
        assertTrue(!uf.union("A", "C"), "Cycle detected");
    }

    private static void testLRUCache() {
        System.out.println("Testing LRUCache...");
        LRUCache<String, String> cache = new LRUCache<>(2);
        cache.put("k1", "v1");
        cache.put("k2", "v2");
        assertEquals("v1", cache.get("k1"), "Get k1 makes it MRU");

        cache.put("k3", "v3"); // Evicts k2
        assertEquals(null, cache.get("k2"), "k2 was evicted");
        assertEquals("v1", cache.get("k1"), "k1 remains");
        assertEquals("v3", cache.get("k3"), "k3 exists");
        assertEquals(1, cache.getEvictionCount(), "Eviction count is 1");
    }

    private static void testRateLimiter() {
        System.out.println("Testing SlidingWindowRateLimiter...");
        SlidingWindowRateLimiter limiter = new SlidingWindowRateLimiter(2, 5.0);
        double now = 1000.0;
        assertTrue(limiter.allow("user1", now), "Request 1 allowed");
        assertTrue(limiter.allow("user1", now + 1.0), "Request 2 allowed");
        assertTrue(!limiter.allow("user1", now + 2.0), "Request 3 blocked");
        assertEquals(0, limiter.getRemaining("user1", now + 2.0), "0 remaining");

        assertTrue(limiter.allow("user1", now + 6.0), "Request allowed after window slides");
    }

    private static void testBloomFilter() {
        System.out.println("Testing BloomFilter...");
        BloomFilter bf = new BloomFilter(100, 0.01);
        bf.add("ref_101");
        bf.add("ref_102");
        assertTrue(bf.contains("ref_101"), "Contains ref_101");
        assertTrue(bf.contains("ref_102"), "Contains ref_102");
        assertTrue(!bf.contains("ref_unknown"), "Does not contain unseen");
    }

    private static void testConsistentHash() {
        System.out.println("Testing ConsistentHash...");
        ConsistentHash ch = new ConsistentHash(50);
        ch.addNode("cluster-node-1");
        ch.addNode("cluster-node-2");
        String n = ch.getNode("user_key_55");
        assertTrue(Arrays.asList("cluster-node-1", "cluster-node-2").contains(n), "Key mapped to valid node");
    }

    private static void testSlidingWindow() {
        System.out.println("Testing SlidingWindow Monotonic Deque...");
        double[] nums = new double[]{1, 3, -1, -3, 5, 3, 6, 7};
        double[] maxs = SlidingWindow.computeSlidingMax(nums, 3);
        double[] expected = new double[]{3, 3, 5, 5, 6, 7};
        assertTrue(Arrays.equals(expected, maxs), "Sliding window max matches benchmark");
    }

    private static void testRetryQueue() {
        System.out.println("Testing RetryQueue...");
        RetryQueue<String> rq = new RetryQueue<>(0.1, 1.0);
        rq.enqueue("job-1", "payload", 2, 0.0);
        assertEquals(1, rq.size(), "Queue size is 1");

        List<?> ready = rq.pollReady();
        assertEquals(1, ready.size(), "Job ready immediately");

        assertTrue(rq.retry("job-1", "Timeout"), "Task re-enqueued");
        assertTrue(!rq.retry("job-1", "Exhausted"), "Task moves to DLQ");
        assertEquals(1, rq.dlqSize(), "DLQ size is 1");
    }
}

