<?php

declare(strict_types=1);

namespace DsaToolbox;

use InvalidArgumentException;

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
class ConsistentHash
{
    private int $replicas;

    /** @var array<int, int> Sorted ring of hash values */
    private array $ring = [];

    /** @var array<int, string> Map of hash => physical node identifier */
    private array $ringMap = [];

    /** @var array<string, bool> Set of registered physical nodes */
    private array $nodes = [];

    /**
     * @param int $replicas Number of virtual node points per physical server (default 100 for balanced distribution)
     * @throws InvalidArgumentException
     */
    public function __construct(int $replicas = 100)
    {
        if ($replicas < 1) {
            throw new InvalidArgumentException("replicas must be at least 1");
        }
        $this->replicas = $replicas;
    }

    /**
     * Adds a physical node and its virtual replicas to the hash ring.
     */
    public function addNode(string $node): void
    {
        if (isset($this->nodes[$node])) {
            return;
        }

        $this->nodes[$node] = true;

        for ($i = 0; $i < $this->replicas; $i++) {
            $vKey = "{$node}#replica_{$i}";
            $hash = $this->hash($vKey);
            $this->ringMap[$hash] = $node;
            $this->ring[] = $hash;
        }

        sort($this->ring, SORT_NUMERIC);
    }

    /**
     * Removes a physical node and its virtual replicas from the hash ring.
     */
    public function removeNode(string $node): void
    {
        if (!isset($this->nodes[$node])) {
            return;
        }

        unset($this->nodes[$node]);

        for ($i = 0; $i < $this->replicas; $i++) {
            $vKey = "{$node}#replica_{$i}";
            $hash = $this->hash($vKey);
            unset($this->ringMap[$hash]);
        }

        // Rebuild sorted ring
        $this->ring = array_keys($this->ringMap);
        sort($this->ring, SORT_NUMERIC);
    }

    /**
     * Finds the target physical node responsible for a given key.
     * Uses binary search to locate the first node with hash >= keyHash on the ring.
     *
     * @param string $key Cache key, session ID, or user ID
     * @return string|null Physical node name, or null if ring is empty
     */
    public function getNode(string $key): ?string
    {
        if (empty($this->ring)) {
            return null;
        }

        $hash = $this->hash($key);
        $index = $this->findFirstGreaterOrEqual($hash);

        // Ring wrap-around: if key hash exceeds max on ring, map to the first node
        if ($index === count($this->ring)) {
            $index = 0;
        }

        $nodeHash = $this->ring[$index];
        return $this->ringMap[$nodeHash] ?? null;
    }

    /**
     * Returns list of all physical nodes.
     *
     * @return array<int, string>
     */
    public function getNodes(): array
    {
        return array_keys($this->nodes);
    }

    public function count(): int
    {
        return count($this->nodes);
    }

    /**
     * Calculates a 32-bit unsigned integer hash using crc32b.
     */
    private function hash(string $key): int
    {
        return (int) sprintf('%u', crc32($key));
    }

    /**
     * Binary search to find lower bound on sorted ring.
     */
    private function findFirstGreaterOrEqual(int $target): int
    {
        $low = 0;
        $high = count($this->ring);

        while ($low < $high) {
            $mid = $low + intdiv($high - $low, 2);
            if ($this->ring[$mid] >= $target) {
                $high = $mid;
            } else {
                $low = $mid + 1;
            }
        }

        return $low;
    }
}

