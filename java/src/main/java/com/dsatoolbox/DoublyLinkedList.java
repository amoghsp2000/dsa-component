package com.dsatoolbox;

import java.util.ArrayList;
import java.util.List;

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
 * Space Complexity: O(N)
 *
 * @param <T> Element type
 */
public class DoublyLinkedList<T> {

    public static class Node<T> {
        public T value;
        public Node<T> prev;
        public Node<T> next;

        public Node(T value) {
            this.value = value;
        }
    }

    private Node<T> head;
    private Node<T> tail;
    private int size = 0;

    public Node<T> prepend(T value) {
        Node<T> node = new Node<>(value);
        if (head == null) {
            head = node;
            tail = node;
        } else {
            node.next = head;
            head.prev = node;
            head = node;
        }
        size++;
        return node;
    }

    public Node<T> append(T value) {
        Node<T> node = new Node<>(value);
        if (tail == null) {
            head = node;
            tail = node;
        } else {
            node.prev = tail;
            tail.next = node;
            tail = node;
        }
        size++;
        return node;
    }

    public void removeNode(Node<T> node) {
        if (node == null) return;

        if (node.prev != null) {
            node.prev.next = node.next;
        } else {
            head = node.next;
        }

        if (node.next != null) {
            node.next.prev = node.prev;
        } else {
            tail = node.prev;
        }

        node.prev = null;
        node.next = null;
        size--;
    }

    public T popFirst() {
        if (head == null) return null;
        T val = head.value;
        removeNode(head);
        return val;
    }

    public T popLast() {
        if (tail == null) return null;
        T val = tail.value;
        removeNode(tail);
        return val;
    }

    public List<T> toList() {
        List<T> result = new ArrayList<>();
        Node<T> curr = head;
        while (curr != null) {
            result.add(curr.value);
            curr = curr.next;
        }
        return result;
    }

    public void clear() {
        Node<T> curr = head;
        while (curr != null) {
            Node<T> next = curr.next;
            curr.prev = null;
            curr.next = null;
            curr = next;
        }
        head = null;
        tail = null;
        size = 0;
    }

    public int size() {
        return size;
    }

    public boolean isEmpty() {
        return size == 0;
    }
}

