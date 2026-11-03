/**
 * Monotonic Queue (Sliding Window Extremes Engine)
 *
 * Traditional DSA Category: Stacks and Queues (Monotonic Deque) + Arrays and Strings
 * Real-World Engineering Use Cases:
 *  - Real-Time Data Processing (streaming metrics, market ticker peaks)
 *  - Logging and Monitoring (sliding 5-minute peak latency or error rate)
 *  - Financial and Payment Systems (sliding drawdown, high-frequency surge detection)
 *  - Performance Optimization (computing window extremes in O(1) amortized vs O(k) linear)
 *
 * Time Complexity:
 *  - push: O(1) amortized
 *  - pop: O(1)
 *  - getMax / getMin: O(1)
 * Space Complexity: O(K) where K is sliding window size
 */
class SlidingWindow {
  constructor() {
    this.queue = [];
    this.maxDeque = [];
  }

  push(val) {
    this.queue.push(val);

    while (this.maxDeque.length > 0 && this.maxDeque[this.maxDeque.length - 1] < val) {
      this.maxDeque.pop();
    }

    this.maxDeque.push(val);
  }

  pop() {
    if (this.queue.length === 0) {
      throw new Error('Sliding window is empty');
    }

    const val = this.queue.shift();

    if (this.maxDeque.length > 0 && this.maxDeque[0] === val) {
      this.maxDeque.shift();
    }

    return val;
  }

  getMax() {
    if (this.maxDeque.length === 0) {
      throw new Error('Sliding window is empty');
    }
    return this.maxDeque[0];
  }

  isEmpty() {
    return this.queue.length === 0;
  }

  get size() {
    return this.queue.length;
  }

  /**
   * Computes sliding window maximums across an array in strictly O(N) total time.
   * @param {Array<number>} nums
   * @param {number} k Window size
   * @returns {Array<number>}
   */
  static computeSlidingMax(nums, k) {
    const n = nums.length;
    if (n === 0 || k <= 0) return [];
    if (k > n) k = n;

    const result = [];
    const deque = []; // Stores indices

    for (let i = 0; i < n; i++) {
      while (deque.length > 0 && deque[0] < i - k + 1) {
        deque.shift();
      }

      while (deque.length > 0 && nums[deque[deque.length - 1]] <= nums[i]) {
        deque.pop();
      }

      deque.push(i);

      if (i >= k - 1) {
        result.push(nums[deque[0]]);
      }
    }

    return result;
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { SlidingWindow };
}

