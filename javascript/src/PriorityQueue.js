/**
 * Priority Queue (Min / Max Binary Heap)
 *
 * Traditional DSA Category: Trees and Graphs
 * Real-World Engineering Use Cases:
 *  - Queue and Background Job Processing (urgent tasks & VIP transactions)
 *  - Scheduling and Task Management (earliest scheduled execution timestamp)
 *  - Real-Time Data Processing (top-K elements, stream prioritization)
 *
 * Time Complexity:
 *  - push: O(log N)
 *  - pop: O(log N)
 *  - peek: O(1)
 *  - size: O(1)
 * Space Complexity: O(N)
 */
class PriorityQueue {
  /**
   * @param {boolean} isMinHeap When true (default), smaller priority values come out first.
   */
  constructor(isMinHeap = true) {
    this.heap = [];
    this.isMinHeap = isMinHeap;
  }

  push(value, priority) {
    this.heap.push({ value, priority });
    this._bubbleUp(this.heap.length - 1);
  }

  pop() {
    if (this.heap.length === 0) {
      throw new Error('Cannot pop from an empty PriorityQueue');
    }

    const root = this.heap[0].value;
    const last = this.heap.pop();

    if (this.heap.length > 0) {
      this.heap[0] = last;
      this._siftDown(0);
    }

    return root;
  }

  peek() {
    if (this.heap.length === 0) {
      throw new Error('Cannot peek into an empty PriorityQueue');
    }
    return this.heap[0].value;
  }

  peekPriority() {
    if (this.heap.length === 0) {
      throw new Error('Cannot peek priority of an empty PriorityQueue');
    }
    return this.heap[0].priority;
  }

  clear() {
    this.heap = [];
  }

  isEmpty() {
    return this.heap.length === 0;
  }

  get size() {
    return this.heap.length;
  }

  _hasPrecedence(p1, p2) {
    return this.isMinHeap ? p1 < p2 : p1 > p2;
  }

  _bubbleUp(index) {
    while (index > 0) {
      const parent = Math.floor((index - 1) / 2);
      if (this._hasPrecedence(this.heap[index].priority, this.heap[parent].priority)) {
        const tmp = this.heap[index];
        this.heap[index] = this.heap[parent];
        this.heap[parent] = tmp;
        index = parent;
      } else {
        break;
      }
    }
  }

  _siftDown(index) {
    const size = this.heap.length;
    while (true) {
      let best = index;
      const left = 2 * index + 1;
      const right = 2 * index + 2;

      if (left < size && this._hasPrecedence(this.heap[left].priority, this.heap[best].priority)) {
        best = left;
      }
      if (right < size && this._hasPrecedence(this.heap[right].priority, this.heap[best].priority)) {
        best = right;
      }

      if (best !== index) {
        const tmp = this.heap[index];
        this.heap[index] = this.heap[best];
        this.heap[best] = tmp;
        index = best;
      } else {
        break;
      }
    }
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { PriorityQueue };
}

