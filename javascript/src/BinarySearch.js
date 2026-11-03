/**
 * Binary Search & Bound Locator
 *
 * Traditional DSA Category: Searching and Sorting
 * Real-World Engineering Use Cases:
 *  - Database Optimization (indexed in-memory table lookup)
 *  - Search and Filtering (log timestamp range queries)
 *  - Financial and Payment Systems (fee tiers, discount brackets, tax schedules)
 *
 * Time Complexity:
 *  - find: O(log N)
 *  - lowerBound: O(log N)
 *  - upperBound: O(log N)
 * Space Complexity: O(1)
 */
class BinarySearch {
  /**
   * Finds the exact index of target in sorted array, or -1 if not found.
   * @param {Array} arr
   * @param {*} target
   * @param {Function|null} comparator fn(a, b) returning <0, 0, >0
   * @returns {number}
   */
  static find(arr, target, comparator = null) {
    let low = 0;
    let high = arr.length - 1;

    while (low <= high) {
      const mid = Math.floor(low + (high - low) / 2);
      const cmp = comparator ? comparator(arr[mid], target) : (arr[mid] === target ? 0 : (arr[mid] < target ? -1 : 1));

      if (cmp === 0) return mid;
      if (cmp < 0) {
        low = mid + 1;
      } else {
        high = mid - 1;
      }
    }

    return -1;
  }

  /**
   * Returns the first index where arr[index] >= target.
   * @param {Array} arr
   * @param {*} target
   * @param {Function|null} comparator
   * @returns {number}
   */
  static lowerBound(arr, target, comparator = null) {
    let low = 0;
    let high = arr.length;

    while (low < high) {
      const mid = Math.floor(low + (high - low) / 2);
      const cmp = comparator ? comparator(arr[mid], target) : (arr[mid] === target ? 0 : (arr[mid] < target ? -1 : 1));

      if (cmp >= 0) {
        high = mid;
      } else {
        low = mid + 1;
      }
    }

    return low;
  }

  /**
   * Returns the first index where arr[index] > target.
   * @param {Array} arr
   * @param {*} target
   * @param {Function|null} comparator
   * @returns {number}
   */
  static upperBound(arr, target, comparator = null) {
    let low = 0;
    let high = arr.length;

    while (low < high) {
      const mid = Math.floor(low + (high - low) / 2);
      const cmp = comparator ? comparator(arr[mid], target) : (arr[mid] === target ? 0 : (arr[mid] < target ? -1 : 1));

      if (cmp > 0) {
        high = mid;
      } else {
        low = mid + 1;
      }
    }

    return low;
  }

  /**
   * Finds the highest tier threshold that is <= value.
   * @param {Array<{threshold: number, value: *}>} sortedTiers
   * @param {number} value
   * @returns {*}
   */
  static findTier(sortedTiers, value) {
    if (!sortedTiers || sortedTiers.length === 0) return null;

    let low = 0;
    let high = sortedTiers.length - 1;
    let bestIndex = -1;

    while (low <= high) {
      const mid = Math.floor(low + (high - low) / 2);
      if (sortedTiers[mid].threshold <= value) {
        bestIndex = mid;
        low = mid + 1;
      } else {
        high = mid - 1;
      }
    }

    return bestIndex !== -1 ? sortedTiers[bestIndex].value : null;
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { BinarySearch };
}

