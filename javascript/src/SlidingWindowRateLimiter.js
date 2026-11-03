/**
 * Sliding Window Rate Limiter
 *
 * Traditional DSA Category: Stacks and Queues (Sliding Window Deque)
 * Real-World Engineering Use Cases:
 *  - Rate Limiting and Throttling (API quota enforcement, anti-scraping)
 *  - API and HTTP Processing (protecting upstream services against surges)
 *  - Financial and Payment Systems (preventing rapid double-spending or card stuffing)
 *
 * Time Complexity:
 *  - allow: O(K) amortized, where K is expired timestamps in the window
 *  - getRemaining: O(1)
 *  - getResetTime: O(1)
 * Space Complexity: O(U * M) where U is active users and M is max requests per window
 */
class SlidingWindowRateLimiter {
  constructor(maxRequests, windowSeconds) {
    if (maxRequests < 1) {
      throw new RangeError('maxRequests must be at least 1');
    }
    if (windowSeconds <= 0) {
      throw new RangeError('windowSeconds must be greater than 0');
    }

    this.maxRequests = maxRequests;
    this.windowSeconds = windowSeconds;
    this.requestLogs = new Map();
  }

  allow(key, now = null) {
    const curr = now !== null ? now : Date.now() / 1000;
    const windowStart = curr - this.windowSeconds;

    this._cleanupKey(key, windowStart);

    if (!this.requestLogs.has(key)) {
      this.requestLogs.set(key, []);
    }

    const log = this.requestLogs.get(key);
    if (log.length < this.maxRequests) {
      log.push(curr);
      return true;
    }

    return false;
  }

  getRemaining(key, now = null) {
    const curr = now !== null ? now : Date.now() / 1000;
    const windowStart = curr - this.windowSeconds;

    this._cleanupKey(key, windowStart);
    const count = this.requestLogs.has(key) ? this.requestLogs.get(key).length : 0;
    return Math.max(0, this.maxRequests - count);
  }

  getResetTime(key, now = null) {
    const curr = now !== null ? now : Date.now() / 1000;
    const windowStart = curr - this.windowSeconds;

    this._cleanupKey(key, windowStart);

    if (!this.requestLogs.has(key) || this.requestLogs.get(key).length < this.maxRequests) {
      return curr;
    }

    const oldest = this.requestLogs.get(key)[0];
    return oldest + this.windowSeconds;
  }

  reset(key) {
    this.requestLogs.delete(key);
  }

  prune(now = null) {
    const curr = now !== null ? now : Date.now() / 1000;
    const windowStart = curr - this.windowSeconds;

    for (const [key, log] of this.requestLogs.entries()) {
      this._cleanupKey(key, windowStart);
      if (log.length === 0) {
        this.requestLogs.delete(key);
      }
    }
  }

  _cleanupKey(key, windowStart) {
    if (!this.requestLogs.has(key)) return;
    const log = this.requestLogs.get(key);
    while (log.length > 0 && log[0] <= windowStart) {
      log.shift();
    }
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { SlidingWindowRateLimiter };
}

