<?php

declare(strict_types=1);

namespace DsaToolbox;

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
class UnionFind
{
    /** @var array<string, string> Map from element to its parent */
    private array $parent = [];

    /** @var array<string, int> Map from root to rank */
    private array $rank = [];

    /** @var array<string, int> Map from root to set size */
    private array $setSize = [];

    private int $componentCount = 0;

    /**
     * Finds the representative root of $element with path compression.
     * Automatically registers previously unseen elements into new singletons.
     *
     * @param string|int $element
     * @return string Root identifier
     */
    public function find(string|int $element): string
    {
        $key = (string) $element;

        if (!isset($this->parent[$key])) {
            $this->parent[$key] = $key;
            $this->rank[$key] = 0;
            $this->setSize[$key] = 1;
            $this->componentCount++;
            return $key;
        }

        if ($this->parent[$key] !== $key) {
            $this->parent[$key] = $this->find($this->parent[$key]); // Path compression
        }

        return $this->parent[$key];
    }

    /**
     * Unites the sets containing $a and $b using union-by-rank.
     *
     * @param string|int $a
     * @param string|int $b
     * @return bool True if sets were distinct and merged; false if they were already connected (cycle detected)
     */
    public function union(string|int $a, string|int $b): bool
    {
        $rootA = $this->find($a);
        $rootB = $this->find($b);

        if ($rootA === $rootB) {
            return false; // Already connected; indicates an edge creates a cycle
        }

        // Union by rank
        if ($this->rank[$rootA] < $this->rank[$rootB]) {
            $this->parent[$rootA] = $rootB;
            $this->setSize[$rootB] += $this->setSize[$rootA];
        } elseif ($this->rank[$rootA] > $this->rank[$rootB]) {
            $this->parent[$rootB] = $rootA;
            $this->setSize[$rootA] += $this->setSize[$rootB];
        } else {
            $this->parent[$rootB] = $rootA;
            $this->setSize[$rootA] += $this->setSize[$rootB];
            $this->rank[$rootA]++;
        }

        $this->componentCount--;
        return true;
    }

    /**
     * Checks if two elements belong to the same connected component.
     */
    public function connected(string|int $a, string|int $b): bool
    {
        return $this->find($a) === $this->find($b);
    }

    /**
     * Returns the size of the set containing $element.
     */
    public function getComponentSize(string|int $element): int
    {
        $root = $this->find($element);
        return $this->setSize[$root];
    }

    /**
     * Returns the total number of disjoint sets.
     */
    public function getComponentCount(): int
    {
        return $this->componentCount;
    }

    /**
     * Returns total number of registered elements.
     */
    public function count(): int
    {
        return count($this->parent);
    }
}

