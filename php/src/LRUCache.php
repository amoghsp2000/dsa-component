<?php

declare(strict_types=1);

namespace DsaToolbox;

use InvalidArgumentException;

/**
 * Internal Node for LRUCache.
 */
class LRUNode
{
    public string|int $key;
    public mixed $value;
    public ?int $expiresAt;
    public ?LRUNode $prev = null;
    public ?LRUNode $next = null;

    public function __construct(string|int $key, mixed $value, ?int $expiresAt = null)
    {
        $this->key = $key;
        $this->value = $value;
        $this->expiresAt = $expiresAt;
    }
}

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
 */
class LRUCache
{
    private int $capacity;

    /** @var array<string|int, LRUNode> */
    private array $map = [];

    private ?LRUNode $head = null; // Most recently used
    private ?LRUNode $tail = null; // Least recently used
    private int $evictionCount = 0;

    /**
     * @param int $capacity Maximum number of items allowed in cache
     * @throws InvalidArgumentException If capacity < 1
     */
    public function __construct(int $capacity)
    {
        if ($capacity < 1) {
            throw new InvalidArgumentException("LRUCache capacity must be greater than 0");
        }
        $this->capacity = $capacity;
    }

    /**
     * Retrieves a value by key, moving it to the most recently used position.
     * Returns null if key does not exist or has expired.
     */
    public function get(string|int $key): mixed
    {
        if (!isset($this->map[$key])) {
            return null;
        }

        $node = $this->map[$key];

        // Check TTL expiration
        if ($node->expiresAt !== null && time() >= $node->expiresAt) {
            $this->delete($key);
            return null;
        }

        $this->moveToHead($node);
        return $node->value;
    }

    /**
     * Inserts or updates a key-value entry with an optional Time-To-Live (seconds).
     *
     * @param string|int $key
     * @param mixed $value
     * @param int|null $ttlSeconds Expiration lifetime in seconds, or null for indefinite
     */
    public function put(string|int $key, mixed $value, ?int $ttlSeconds = null): void
    {
        $expiresAt = $ttlSeconds !== null ? (time() + $ttlSeconds) : null;

        if (isset($this->map[$key])) {
            $node = $this->map[$key];
            $node->value = $value;
            $node->expiresAt = $expiresAt;
            $this->moveToHead($node);
            return;
        }

        $node = new LRUNode($key, $value, $expiresAt);
        $this->map[$key] = $node;
        $this->attachHead($node);

        if (count($this->map) > $this->capacity) {
            $this->evictTail();
        }
    }

    /**
     * Inspects a value without updating its recency.
     */
    public function peek(string|int $key): mixed
    {
        if (!isset($this->map[$key])) {
            return null;
        }
        $node = $this->map[$key];
        if ($node->expiresAt !== null && time() >= $node->expiresAt) {
            return null;
        }
        return $node->value;
    }

    /**
     * Checks whether a key is present and not expired.
     */
    public function has(string|int $key): bool
    {
        return $this->get($key) !== null;
    }

    /**
     * Removes an entry by key.
     */
    public function delete(string|int $key): bool
    {
        if (!isset($this->map[$key])) {
            return false;
        }

        $node = $this->map[$key];
        $this->detachNode($node);
        unset($this->map[$key]);
        return true;
    }

    public function count(): int
    {
        return count($this->map);
    }

    public function getCapacity(): int
    {
        return $this->capacity;
    }

    public function getEvictionCount(): int
    {
        return $this->evictionCount;
    }

    public function clear(): void
    {
        $this->map = [];
        $this->head = null;
        $this->tail = null;
        $this->evictionCount = 0;
    }

    private function moveToHead(LRUNode $node): void
    {
        if ($this->head === $node) {
            return;
        }
        $this->detachNode($node);
        $this->attachHead($node);
    }

    private function attachHead(LRUNode $node): void
    {
        $node->prev = null;
        $node->next = $this->head;

        if ($this->head !== null) {
            $this->head->prev = $node;
        }
        $this->head = $node;

        if ($this->tail === null) {
            $this->tail = $node;
        }
    }

    private function detachNode(LRUNode $node): void
    {
        if ($node->prev !== null) {
            $node->prev->next = $node->next;
        } else {
            $this->head = $node->next;
        }

        if ($node->next !== null) {
            $node->next->prev = $node->prev;
        } else {
            $this->tail = $node->prev;
        }

        $node->prev = null;
        $node->next = null;
    }

    private function evictTail(): void
    {
        if ($this->tail === null) {
            return;
        }
        $lru = $this->tail;
        $this->detachNode($lru);
        unset($this->map[$lru->key]);
        $this->evictionCount++;
    }
}

