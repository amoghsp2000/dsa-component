<?php

declare(strict_types=1);

namespace DsaToolbox\Examples;

require_once __DIR__ . '/../src/LRUCache.php';
require_once __DIR__ . '/../src/ConsistentHash.php';
require_once __DIR__ . '/../src/DoublyLinkedList.php';

use DsaToolbox\LRUCache;
use DsaToolbox\ConsistentHash;
use DsaToolbox\DoublyLinkedList;

/**
 * Distributed Sharded In-Memory Cache Cluster Example
 *
 * Demonstrates:
 *  1. ConsistentHash: Deterministically partitions cache keys across server nodes with virtual replicas.
 *  2. LRUCache: Per-node fixed-capacity bounded cache with TTL and O(1) eviction.
 *  3. DoublyLinkedList: Real-time user access audit trail.
 */
class DistributedCacheCluster
{
    private ConsistentHash $hashRing;

    /** @var array<string, LRUCache> Map of node name => local LRUCache instance */
    private array $nodes = [];

    private DoublyLinkedList $accessAuditLog;
    private int $perNodeCapacity;

    public function __construct(array $initialNodes, int $perNodeCapacity = 500)
    {
        $this->hashRing = new ConsistentHash(replicas: 100);
        $this->accessAuditLog = new DoublyLinkedList();
        $this->perNodeCapacity = $perNodeCapacity;

        foreach ($initialNodes as $nodeName) {
            $this->addNode($nodeName);
        }
    }

    public function addNode(string $nodeName): void
    {
        $this->hashRing->addNode($nodeName);
        $this->nodes[$nodeName] = new LRUCache($this->perNodeCapacity);
    }

    public function removeNode(string $nodeName): void
    {
        $this->hashRing->removeNode($nodeName);
        unset($this->nodes[$nodeName]);
    }

    /**
     * Stores a key-value entry in the cluster.
     */
    public function set(string $key, mixed $value, ?int $ttlSeconds = null): string
    {
        $targetNode = $this->hashRing->getNode($key);
        if ($targetNode === null || !isset($this->nodes[$targetNode])) {
            throw new \RuntimeException("No active cache nodes available");
        }

        $this->nodes[$targetNode]->put($key, $value, $ttlSeconds);
        $this->accessAuditLog->append(['action' => 'SET', 'key' => $key, 'node' => $targetNode, 'time' => time()]);

        return $targetNode;
    }

    /**
     * Retrieves an entry from the designated node.
     */
    public function get(string $key): mixed
    {
        $targetNode = $this->hashRing->getNode($key);
        if ($targetNode === null || !isset($this->nodes[$targetNode])) {
            return null;
        }

        $val = $this->nodes[$targetNode]->get($key);
        $this->accessAuditLog->append(['action' => 'GET', 'key' => $key, 'hit' => ($val !== null), 'node' => $targetNode]);
        return $val;
    }

    /**
     * Inspects cluster statistics.
     */
    public function getStats(): array
    {
        $stats = [];
        foreach ($this->nodes as $nodeName => $cache) {
            $stats[$nodeName] = [
                'items' => $cache->count(),
                'evictions' => $cache->getEvictionCount(),
                'capacity' => $cache->getCapacity(),
            ];
        }
        return $stats;
    }
}

// ---------------- Demo Execution ----------------
if (php_sapi_name() === 'cli' && basename(__FILE__) === basename($_SERVER['SCRIPT_FILENAME'] ?? '')) {
    echo "=== Running Distributed Consistent-Hashed LRU Cache Demo ===\n\n";

    $cluster = new DistributedCacheCluster(['redis-node-1', 'redis-node-2', 'redis-node-3'], perNodeCapacity: 3);

    // Write several keys
    $keys = ['session_user_401', 'session_user_402', 'session_user_403', 'session_user_404', 'session_user_405'];
    foreach ($keys as $k) {
        $node = $cluster->set($k, ['user_id' => $k, 'login_time' => time()], ttlSeconds: 60);
        echo "Stored '$k' -> routed to [$node]\n";
    }

    echo "\nCluster Node Distribution:\n";
    print_r($cluster->getStats());

    // Retrieve a key
    $val = $cluster->get('session_user_401');
    echo "Retrieved 'session_user_401': " . json_encode($val) . "\n";

    echo "\nDistributed Cache demo finished!\n";
}

