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
class UnionFind {
  constructor() {
    this.parent = new Map();
    this.rank = new Map();
    this.setSize = new Map();
    this.componentCount = 0;
  }

  find(element) {
    const key = String(element);

    if (!this.parent.has(key)) {
      this.parent.set(key, key);
      this.rank.set(key, 0);
      this.setSize.set(key, 1);
      this.componentCount++;
      return key;
    }

    if (this.parent.get(key) !== key) {
      this.parent.set(key, this.find(this.parent.get(key))); // Path compression
    }

    return this.parent.get(key);
  }

  union(a, b) {
    const rootA = this.find(a);
    const rootB = this.find(b);

    if (rootA === rootB) {
      return false; // Cycle detected
    }

    const rankA = this.rank.get(rootA);
    const rankB = this.rank.get(rootB);
    const sizeA = this.setSize.get(rootA);
    const sizeB = this.setSize.get(rootB);

    if (rankA < rankB) {
      this.parent.set(rootA, rootB);
      this.setSize.set(rootB, sizeB + sizeA);
    } else if (rankA > rankB) {
      this.parent.set(rootB, rootA);
      this.setSize.set(rootA, sizeA + sizeB);
    } else {
      this.parent.set(rootB, rootA);
      this.setSize.set(rootA, sizeA + sizeB);
      this.rank.set(rootA, rankA + 1);
    }

    this.componentCount--;
    return true;
  }

  connected(a, b) {
    return this.find(a) === this.find(b);
  }

  getComponentSize(element) {
    const root = this.find(element);
    return this.setSize.get(root);
  }

  get size() {
    return this.parent.size;
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { UnionFind };
}

