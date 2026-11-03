package com.dsatoolbox;

import java.util.HashMap;
import java.util.Map;

/**
 * Disjoint Set Union (Union-Find) with Path Compression and Union by Rank
 *
 * Traditional DSA Category: Advanced Data Structures
 * Real-World Engineering Use Cases:
 *  - Networking and Routing (mesh connectivity, cluster partition checking)
 *  - Distributed Systems (server group membership, partition split detection)
 *  - Transaction Processing (circular transaction detection, account fraud rings)
 *
 * Time Complexity:
 *  - find: O(alpha(N)) amortized (effectively constant time)
 *  - union: O(alpha(N)) amortized
 *  - connected: O(alpha(N)) amortized
 * Space Complexity: O(N)
 */
public class UnionFind {

    private final Map<String, String> parent = new HashMap<>();
    private final Map<String, Integer> rank = new HashMap<>();
    private final Map<String, Integer> setSize = new HashMap<>();
    private int componentCount = 0;

    public String find(String element) {
        if (!parent.containsKey(element)) {
            parent.put(element, element);
            rank.put(element, 0);
            setSize.put(element, 1);
            componentCount++;
            return element;
        }

        if (!parent.get(element).equals(element)) {
            parent.put(element, find(parent.get(element))); // Path compression
        }

        return parent.get(element);
    }

    public boolean union(String a, String b) {
        String rootA = find(a);
        String rootB = find(b);

        if (rootA.equals(rootB)) {
            return false; // Cycle detected
        }

        int rankA = rank.get(rootA);
        int rankB = rank.get(rootB);
        int sizeA = setSize.get(rootA);
        int sizeB = setSize.get(rootB);

        if (rankA < rankB) {
            parent.put(rootA, rootB);
            setSize.put(rootB, sizeB + sizeA);
        } else if (rankA > rankB) {
            parent.put(rootB, rootA);
            setSize.put(rootA, sizeA + sizeB);
        } else {
            parent.put(rootB, rootA);
            setSize.put(rootA, sizeA + sizeB);
            rank.put(rootA, rankA + 1);
        }

        componentCount--;
        return true;
    }

    public boolean connected(String a, String b) {
        return find(a).equals(find(b));
    }

    public int getComponentSize(String element) {
        String root = find(element);
        return setSize.get(root);
    }

    public int getComponentCount() {
        return componentCount;
    }

    public int size() {
        return parent.size();
    }
}

