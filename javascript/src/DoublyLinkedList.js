/**
 * Doubly Linked List Node
 */
class DoublyLinkedListNode {
  constructor(value) {
    this.value = value;
    this.prev = null;
    this.next = null;
  }
}

/**
 * Doubly Linked List
 *
 * Traditional DSA Category: Linked Lists
 * Real-World Engineering Use Cases:
 *  - Caching and Memory Management (backbone for LRU / LFU cache eviction chains)
 *  - Queue and Background Job Processing (bidirectional worker buffers)
 *
 * Time Complexity:
 *  - prepend: O(1)
 *  - append: O(1)
 *  - removeNode: O(1) given direct node reference
 *  - popFirst: O(1)
 *  - popLast: O(1)
 * Space Complexity: O(N) where N is number of nodes
 */
class DoublyLinkedList {
  constructor() {
    this.head = null;
    this.tail = null;
    this.size = 0;
  }

  /**
   * Prepends a value to the head of the list in O(1) time.
   * @param {*} value
   * @returns {DoublyLinkedListNode}
   */
  prepend(value) {
    const node = new DoublyLinkedListNode(value);
    if (!this.head) {
      this.head = node;
      this.tail = node;
    } else {
      node.next = this.head;
      this.head.prev = node;
      this.head = node;
    }
    this.size++;
    return node;
  }

  /**
   * Appends a value to the tail of the list in O(1) time.
   * @param {*} value
   * @returns {DoublyLinkedListNode}
   */
  append(value) {
    const node = new DoublyLinkedListNode(value);
    if (!this.tail) {
      this.head = node;
      this.tail = node;
    } else {
      node.prev = this.tail;
      this.tail.next = node;
      this.tail = node;
    }
    this.size++;
    return node;
  }

  /**
   * Removes an arbitrary node in O(1) time given its direct reference.
   * @param {DoublyLinkedListNode} node
   */
  removeNode(node) {
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
    this.size--;
  }

  /**
   * Removes and returns the value at the head of the list.
   * @returns {*|null}
   */
  popFirst() {
    if (!this.head) return null;
    const val = this.head.value;
    this.removeNode(this.head);
    return val;
  }

  /**
   * Removes and returns the value at the tail of the list.
   * @returns {*|null}
   */
  popLast() {
    if (!this.tail) return null;
    const val = this.tail.value;
    this.removeNode(this.tail);
    return val;
  }

  /**
   * Converts the linked list to a standard JavaScript Array.
   * @returns {Array}
   */
  toArray() {
    const arr = [];
    let curr = this.head;
    while (curr) {
      arr.push(curr.value);
      curr = curr.next;
    }
    return arr;
  }

  clear() {
    let curr = this.head;
    while (curr) {
      const next = curr.next;
      curr.prev = null;
      curr.next = null;
      curr = next;
    }
    this.head = null;
    this.tail = null;
    this.size = 0;
  }

  get length() {
    return this.size;
  }

  isEmpty() {
    return this.size === 0;
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { DoublyLinkedList, DoublyLinkedListNode };
}

