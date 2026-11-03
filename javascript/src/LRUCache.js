/**
 * Internal Node for LRUCache.
 */
class LRUNode {
  constructor(key, value, expiresAt = null) {
    this.key = key;
    this.value = value;
    this.expiresAt = expiresAt;
    this.prev = null;
    this.next = null;
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
class LRUCache {
  /**
   * @param {number} capacity
   */
  constructor(capacity) {
    if (capacity < 1) {
      throw new RangeError('LRUCache capacity must be greater than 0');
    }
    this.capacity = capacity;
    this.map = new Map();
    this.head = null; // Most recently used
    this.tail = null; // Least recently used
    this.evictionCount = 0;
  }

  get(key) {
    if (!this.map.has(key)) return null;

    const node = this.map.get(key);
    if (node.expiresAt !== null && Date.now() >= node.expiresAt) {
      this.delete(key);
      return null;
    }

    this._moveToHead(node);
    return node.value;
  }

  put(key, value, ttlSeconds = null) {
    const expiresAt = ttlSeconds !== null ? Date.now() + ttlSeconds * 1000 : null;

    if (this.map.has(key)) {
      const node = this.map.get(key);
      node.value = value;
      node.expiresAt = expiresAt;
      this._moveToHead(node);
      return;
    }

    const node = new LRUNode(key, value, expiresAt);
    this.map.set(key, node);
    this._attachHead(node);

    if (this.map.size > this.capacity) {
      this._evictTail();
    }
  }

  peek(key) {
    if (!this.map.has(key)) return null;
    const node = this.map.get(key);
    if (node.expiresAt !== null && Date.now() >= node.expiresAt) {
      return null;
    }
    return node.value;
  }

  has(key) {
    return this.get(key) !== null;
  }

  delete(key) {
    if (!this.map.has(key)) return false;
    const node = this.map.get(key);
    this._detachNode(node);
    this.map.delete(key);
    return true;
  }

  clear() {
    this.map.clear();
    this.head = null;
    this.tail = null;
    this.evictionCount = 0;
  }

  get size() {
    return this.map.size;
  }

  _moveToHead(node) {
    if (this.head === node) return;
    this._detachNode(node);
    this._attachHead(node);
  }

  _attachHead(node) {
    node.prev = null;
    node.next = this.head;
    if (this.head) {
      this.head.prev = node;
    }
    this.head = node;
    if (!this.tail) {
      this.tail = node;
    }
  }

  _detachNode(node) {
    if (node.prev) {
      node.prev.next = node.next;
    } else {
      this.head = node.next;
    }

    if (node.next) {
      node.next.prev = node.prev;
    } else {
      this.tail = node.prev;
    }

    node.prev = null;
    node.next = null;
  }

  _evictTail() {
    if (!this.tail) return;
    const lru = this.tail;
    this._detachNode(lru);
    this.map.delete(lru.key);
    this.evictionCount++;
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { LRUCache };
}

