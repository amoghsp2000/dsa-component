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
class ConsistentHash {
  constructor(replicas = 100) {
    if (replicas < 1) {
      throw new RangeError('replicas must be at least 1');
    }
    this.replicas = replicas;
    this.ring = [];
    this.ringMap = new Map();
    this.nodes = new Set();
  }

  addNode(node) {
    if (this.nodes.has(node)) return;

    this.nodes.add(node);

    for (let i = 0; i < this.replicas; i++) {
      const vKey = `${node}#replica_${i}`;
      const hash = this._hash(vKey);
      this.ringMap.set(hash, node);
      this._insertSorted(this.ring, hash);
    }
  }

  removeNode(node) {
    if (!this.nodes.has(node)) return;

    this.nodes.delete(node);

    for (let i = 0; i < this.replicas; i++) {
      const vKey = `${node}#replica_${i}`;
      const hash = this._hash(vKey);
      this.ringMap.delete(hash);
    }

    this.ring = Array.from(this.ringMap.keys()).sort((a, b) => a - b);
  }

  getNode(key) {
    if (this.ring.length === 0) return null;

    const hash = this._hash(key);
    let idx = this._binarySearchLowerBound(this.ring, hash);

    // Wrap around to the start of the ring
    if (idx === this.ring.length) {
      idx = 0;
    }

    const nodeHash = this.ring[idx];
    return this.ringMap.get(nodeHash) || null;
  }

  getNodes() {
    return Array.from(this.nodes);
  }

  get size() {
    return this.nodes.size;
  }

  _hash(key) {
    // FNV-1a 32-bit unsigned
    let h = 2166136261 >>> 0;
    for (let i = 0; i < key.length; i++) {
      h ^= key.charCodeAt(i);
      h = Math.imul(h, 16777619) >>> 0;
    }
    return h;
  }

  _binarySearchLowerBound(arr, target) {
    let low = 0;
    let high = arr.length;

    while (low < high) {
      const mid = Math.floor(low + (high - low) / 2);
      if (arr[mid] >= target) {
        high = mid;
      } else {
        low = mid + 1;
      }
    }

    return low;
  }

  _insertSorted(arr, val) {
    let low = 0;
    let high = arr.length;
    while (low < high) {
      const mid = Math.floor(low + (high - low) / 2);
      if (arr[mid] >= val) {
        high = mid;
      } else {
        low = mid + 1;
      }
    }
    arr.splice(low, 0, val);
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { ConsistentHash };
}

