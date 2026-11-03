/**
 * Bloom Filter
 *
 * Traditional DSA Category: Advanced Data Structures (Bit Manipulation + Hashing)
 * Real-World Engineering Use Cases:
 *  - Data Deduplication and Validation (idempotency key pre-filtering before DB queries)
 *  - Database Optimization (eliminating disk reads for non-existent records / anti-cache penetration)
 *  - Financial and Payment Systems (checking transaction references, duplicate order prevention)
 *
 * Guarantees:
 *  - False Negatives: NEVER (if contains() returns false, item is 100% not in set)
 *  - False Positives: Tunable probability (e.g. 1%, 0.1%)
 *
 * Time Complexity:
 *  - add: O(K) where K is number of hash iterations
 *  - contains: O(K)
 * Space Complexity: O(M) bits
 */
class BloomFilter {
  constructor(expectedElements, falsePositiveRate = 0.01) {
    if (expectedElements < 1) {
      throw new RangeError('expectedElements must be at least 1');
    }
    if (falsePositiveRate <= 0 || falsePositiveRate >= 1) {
      throw new RangeError('falsePositiveRate must be strictly between 0 and 1');
    }

    const rawM = Math.ceil(-(expectedElements * Math.log(falsePositiveRate)) / (Math.LN2 * Math.LN2));
    this.bitSize = Math.max(64, Math.ceil(rawM / 8) * 8);
    this.hashCount = Math.max(1, Math.round((this.bitSize / expectedElements) * Math.LN2));

    this.bitArray = new Uint8Array(this.bitSize / 8);
    this.itemCount = 0;
  }

  add(item) {
    const [h1, h2] = this._hashPair(String(item));

    for (let i = 0; i < this.hashCount; i++) {
      // Kirsch-Mitzenmacher optimization
      const bitIndex = Math.abs((h1 + i * h2) % this.bitSize);
      this._setBit(bitIndex);
    }

    this.itemCount++;
  }

  contains(item) {
    const [h1, h2] = this._hashPair(String(item));

    for (let i = 0; i < this.hashCount; i++) {
      const bitIndex = Math.abs((h1 + i * h2) % this.bitSize);
      if (!this._testBit(bitIndex)) {
        return false;
      }
    }

    return true;
  }

  get size() {
    return this.itemCount;
  }

  estimatedFalsePositiveRate() {
    if (this.itemCount === 0) return 0.0;
    return Math.pow(1 - Math.exp(-(this.hashCount * this.itemCount) / this.bitSize), this.hashCount);
  }

  clear() {
    this.bitArray.fill(0);
    this.itemCount = 0;
  }

  _setBit(index) {
    const bytePos = Math.floor(index / 8);
    const bitPos = index % 8;
    this.bitArray[bytePos] |= (1 << bitPos);
  }

  _testBit(index) {
    const bytePos = Math.floor(index / 8);
    const bitPos = index % 8;
    return (this.bitArray[bytePos] & (1 << bitPos)) !== 0;
  }

  /**
   * Generates two independent 32-bit hashes using FNV-1a and DJB2 algorithms.
   */
  _hashPair(str) {
    // 1. FNV-1a hash
    let h1 = 2166136261 >>> 0;
    for (let i = 0; i < str.length; i++) {
      h1 ^= str.charCodeAt(i);
      h1 = Math.imul(h1, 16777619) >>> 0;
    }

    // 2. DJB2 hash
    let h2 = 5381 >>> 0;
    for (let i = 0; i < str.length; i++) {
      h2 = ((h2 << 5) + h2 + str.charCodeAt(i)) >>> 0;
    }

    return [h1, h2];
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { BloomFilter };
}

