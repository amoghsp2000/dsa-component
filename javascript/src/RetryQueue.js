/**
 * Task item within RetryQueue.
 */
class RetryTask {
  constructor(id, payload, maxAttempts, nextExecutionTime) {
    this.id = id;
    this.payload = payload;
    this.attempts = 0;
    this.maxAttempts = maxAttempts;
    this.nextExecutionTime = nextExecutionTime;
    this.lastError = null;
  }
}

/**
 * Retry Queue with Exponential Backoff & Full Jitter
 *
 * Traditional DSA Category: Stacks and Queues (Priority Min-Heap) + Greedy Algorithms
 * Real-World Engineering Use Cases:
 *  - Queue and Background Job Processing (resilient task retries, worker orchestration)
 *  - Financial and Payment Systems (payment gateway webhooks, recharge retries)
 *  - API and HTTP Processing (mitigating thundering herd during third-party outages)
 *
 * Backoff Formula (Full Jitter):
 *   sleep = random_float(0, min(maxBackoff, baseBackoff * (2 ^ attempt)))
 *
 * Time Complexity:
 *  - enqueue: O(log N)
 *  - pollReady: O(log N) per ready item, O(1) peek when none ready
 *  - retry: O(log N)
 * Space Complexity: O(N)
 */
class RetryQueue {
  constructor(baseBackoff = 1.0, maxBackoff = 60.0) {
    if (baseBackoff <= 0 || maxBackoff < baseBackoff) {
      throw new RangeError('Invalid backoff: base must be > 0 and <= max');
    }
    this.baseBackoff = baseBackoff;
    this.maxBackoff = maxBackoff;
    this.heap = []; // Min-heap: array of { priority: number, task: RetryTask }
    this.taskIndex = new Map();
    this.deadLetterQueue = [];
  }

  enqueue(id, payload, maxAttempts = 5, delaySeconds = 0.0) {
    const now = Date.now() / 1000;
    const due = now + Math.max(0, delaySeconds);

    const task = new RetryTask(id, payload, maxAttempts, due);
    this.taskIndex.set(id, task);
    this._heapPush(task, due);
  }

  retry(id, reason = null) {
    if (!this.taskIndex.has(id)) return false;

    const task = this.taskIndex.get(id);
    task.attempts++;
    task.lastError = reason;

    if (task.attempts >= task.maxAttempts) {
      this.deadLetterQueue.push(task);
      this.taskIndex.delete(id);
      return false;
    }

    const multiplier = Math.pow(2, task.attempts);
    const calculatedBackoff = Math.min(this.maxBackoff, this.baseBackoff * multiplier);
    const randomDelay = Math.random() * calculatedBackoff;

    task.nextExecutionTime = Date.now() / 1000 + randomDelay;
    this._heapPush(task, task.nextExecutionTime);
    return true;
  }

  pollReady(now = null) {
    const curr = now !== null ? now : Date.now() / 1000;
    const ready = [];

    while (this.heap.length > 0) {
      const nextDue = this.heap[0].priority;
      if (nextDue > curr) {
        break;
      }

      const task = this._heapPop();
      if (task && this.taskIndex.has(task.id)) {
        ready.push(task);
      }
    }

    return ready;
  }

  peekNextDueTime() {
    return this.heap.length > 0 ? this.heap[0].priority : null;
  }

  get dlq() {
    return this.deadLetterQueue;
  }

  get size() {
    return this.taskIndex.size;
  }

  get dlqSize() {
    return this.deadLetterQueue.length;
  }

  _heapPush(task, priority) {
    this.heap.push({ priority, task });
    let idx = this.heap.length - 1;

    while (idx > 0) {
      const parent = Math.floor((idx - 1) / 2);
      if (this.heap[idx].priority < this.heap[parent].priority) {
        const tmp = this.heap[idx];
        this.heap[idx] = this.heap[parent];
        this.heap[parent] = tmp;
        idx = parent;
      } else {
        break;
      }
    }
  }

  _heapPop() {
    if (this.heap.length === 0) return null;
    const root = this.heap[0].task;
    const last = this.heap.pop();

    if (this.heap.length > 0) {
      this.heap[0] = last;
      this._siftDown(0);
    }

    return root;
  }

  _siftDown(idx) {
    const size = this.heap.length;
    while (true) {
      let smallest = idx;
      const left = 2 * idx + 1;
      const right = 2 * idx + 2;

      if (left < size && this.heap[left].priority < this.heap[smallest].priority) {
        smallest = left;
      }
      if (right < size && this.heap[right].priority < this.heap[smallest].priority) {
        smallest = right;
      }

      if (smallest !== idx) {
        const tmp = this.heap[idx];
        this.heap[idx] = this.heap[smallest];
        this.heap[smallest] = tmp;
        idx = smallest;
      } else {
        break;
      }
    }
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { RetryQueue, RetryTask };
}

