package com.dsatoolbox;

import java.util.HashMap;
import java.util.Map;

/**
 * LRU Cache (Least Recently Used) with Optional TTL
 *
 * Traditional DSA Category: Hash Tables + Linked Lists
 * Real-World Engineering Use Cases:
 *  - Caching and Memory Management (strict memory bounds, high hit ratio)
 *  - API and HTTP Processing (API response caching, user authorization tokens)
 *  - Database Optimization (eliminating repetitive database queries)
 *  - Performance Optimization (sub-millisecond hot data retrieval)
 *
 * Time Complexity:
 *  - get: O(1)
 *  - put: O(1)
 *  - delete: O(1)
 *  - has: O(1)
 * Space Complexity: O(capacity)
 *
 * @param <K> Key type
 * @param <V> Value type
 */
public class LRUCache<K, V> {

    private static class Node<K, V> {
        final K key;
        V value;
        Long expiresAt; // Epoch milliseconds or null
        Node<K, V> prev;
        Node<K, V> next;

        Node(K key, V value, Long expiresAt) {
            this.key = key;
            this.value = value;
            this.expiresAt = expiresAt;
        }
    }

    private final int capacity;
    private final Map<K, Node<K, V>> map = new HashMap<>();
    private Node<K, V> head; // MRU
    private Node<K, V> tail; // LRU
    private int evictionCount = 0;

    public LRUCache(int capacity) {
        if (capacity < 1) {
            throw new IllegalArgumentException("LRUCache capacity must be greater than 0");
        }
        this.capacity = capacity;
    }

    public synchronized V get(K key) {
        Node<K, V> node = map.get(key);
        if (node == null) return null;

        if (node.expiresAt != null && System.currentTimeMillis() >= node.expiresAt) {
            delete(key);
            return null;
        }

        moveToHead(node);
        return node.value;
    }

    public synchronized void put(K key, V value) {
        put(key, value, null);
    }

    public synchronized void put(K key, V value, Long ttlSeconds) {
        Long expiresAt = ttlSeconds != null ? System.currentTimeMillis() + (ttlSeconds * 1000) : null;

        if (map.containsKey(key)) {
            Node<K, V> node = map.get(key);
            node.value = value;
            node.expiresAt = expiresAt;
            moveToHead(node);
            return;
        }

        Node<K, V> node = new Node<>(key, value, expiresAt);
        map.put(key, node);
        attachHead(node);

        if (map.size() > capacity) {
            evictTail();
        }
    }

    public synchronized V peek(K key) {
        Node<K, V> node = map.get(key);
        if (node == null) return null;
        if (node.expiresAt != null && System.currentTimeMillis() >= node.expiresAt) {
            return null;
        }
        return node.value;
    }

    public synchronized boolean has(K key) {
        return get(key) != null;
    }

    public synchronized boolean delete(K key) {
        Node<K, V> node = map.remove(key);
        if (node == null) return false;
        detachNode(node);
        return true;
    }

    public synchronized void clear() {
        map.clear();
        head = null;
        tail = null;
        evictionCount = 0;
    }

    public synchronized int size() {
        return map.size();
    }

    public int getCapacity() {
        return capacity;
    }

    public synchronized int getEvictionCount() {
        return evictionCount;
    }

    private void moveToHead(Node<K, V> node) {
        if (head == node) return;
        detachNode(node);
        attachHead(node);
    }

    private void attachHead(Node<K, V> node) {
        node.prev = null;
        node.next = head;
        if (head != null) {
            head.prev = node;
        }
        head = node;
        if (tail == null) {
            tail = node;
        }
    }

    private void detachNode(Node<K, V> node) {
        if (node.prev != null) {
            node.prev.next = node.next;
        } else {
            head = node.next;
        }

        if (node.next != null) {
            node.next.prev = node.prev;
        } else {
            tail = node.prev;
        }

        node.prev = null;
        node.next = null;
    }

    private void evictTail() {
        if (tail == null) return;
        Node<K, V> lru = tail;
        detachNode(lru);
        map.remove(lru.key);
        evictionCount++;
    }
}

