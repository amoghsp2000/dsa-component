package com.dsatoolbox;

import java.nio.charset.StandardCharsets;
import java.util.*;
import java.util.zip.CRC32;

/**
 * Consistent Hash Ring with Virtual Nodes
 *
 * Traditional DSA Category: Advanced Data Structures (Searching & Hash Tables)
 * Real-World Engineering Use Cases:
 *  - Distributed Systems (distributing cache keys across Redis / Memcached clusters)
 *  - Caching and Memory Management (minimizing cache misses on cluster scaling)
 *  - Networking and Routing (sticky session routing and gateway sharding)
 *
 * Time Complexity:
 *  - addNode: O(V * log(N * V)) where V is virtual replicas, N is server count
 *  - removeNode: O(V * log(N * V))
 *  - getNode: O(log(N * V)) via binary search on ring
 * Space Complexity: O(N * V)
 */
public class ConsistentHash {

    private final int replicas;
    private final List<Long> ring = new ArrayList<>();
    private final Map<Long, String> ringMap = new HashMap<>();
    private final Set<String> nodes = new HashSet<>();

    public ConsistentHash() {
        this(100);
    }

    public ConsistentHash(int replicas) {
        if (replicas < 1) {
            throw new IllegalArgumentException("replicas must be at least 1");
        }
        this.replicas = replicas;
    }

    public synchronized void addNode(String node) {
        if (nodes.contains(node)) return;
        nodes.add(node);

        for (int i = 0; i < replicas; i++) {
            String vKey = node + "#replica_" + i;
            long hash = hash(vKey);
            ringMap.put(hash, node);
            int idx = Collections.binarySearch(ring, hash);
            if (idx < 0) {
                idx = -idx - 1;
            }
            ring.add(idx, hash);
        }
    }

    public synchronized void removeNode(String node) {
        if (!nodes.contains(node)) return;
        nodes.remove(node);

        for (int i = 0; i < replicas; i++) {
            String vKey = node + "#replica_" + i;
            long hash = hash(vKey);
            ringMap.remove(hash);
        }

        ring.clear();
        ring.addAll(ringMap.keySet());
        Collections.sort(ring);
    }

    public synchronized String getNode(String key) {
        if (ring.isEmpty()) return null;

        long hash = hash(key);
        int idx = Collections.binarySearch(ring, hash);
        if (idx < 0) {
            idx = -idx - 1;
        }

        // Ring wrap-around
        if (idx == ring.size()) {
            idx = 0;
        }

        long nodeHash = ring.get(idx);
        return ringMap.get(nodeHash);
    }

    public synchronized Set<String> getNodes() {
        return new HashSet<>(nodes);
    }

    public synchronized int size() {
        return nodes.size();
    }

    private long hash(String key) {
        CRC32 crc = new CRC32();
        crc.update(key.getBytes(StandardCharsets.UTF_8));
        return crc.getValue();
    }
}

