package com.dsatoolbox;

import java.util.ArrayList;
import java.util.List;
import java.util.NoSuchElementException;

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
 *
 * @param <T> Element payload type
 */
public class PriorityQueue<T> {

    private static class Node<T> {
        final T value;
        final double priority;

        Node(T value, double priority) {
            this.value = value;
            this.priority = priority;
        }
    }

    private final List<Node<T>> heap = new ArrayList<>();
    private final boolean isMinHeap;

    public PriorityQueue() {
        this(true);
    }

    public PriorityQueue(boolean isMinHeap) {
        this.isMinHeap = isMinHeap;
    }

    public void push(T value, double priority) {
        heap.add(new Node<>(value, priority));
        bubbleUp(heap.size() - 1);
    }

    public T pop() {
        if (heap.isEmpty()) {
            throw new NoSuchElementException("Cannot pop from an empty PriorityQueue");
        }

        T root = heap.get(0).value;
        Node<T> last = heap.remove(heap.size() - 1);

        if (!heap.isEmpty()) {
            heap.set(0, last);
            siftDown(0);
        }

        return root;
    }

    public T peek() {
        if (heap.isEmpty()) {
            throw new NoSuchElementException("Cannot peek into an empty PriorityQueue");
        }
        return heap.get(0).value;
    }

    public double peekPriority() {
        if (heap.isEmpty()) {
            throw new NoSuchElementException("Cannot peek priority of an empty PriorityQueue");
        }
        return heap.get(0).priority;
    }

    public int size() {
        return heap.size();
    }

    public boolean isEmpty() {
        return heap.isEmpty();
    }

    public void clear() {
        heap.clear();
    }

    private boolean hasPrecedence(double p1, double p2) {
        return isMinHeap ? (p1 < p2) : (p1 > p2);
    }

    private void bubbleUp(int index) {
        while (index > 0) {
            int parent = (index - 1) / 2;
            if (hasPrecedence(heap.get(index).priority, heap.get(parent).priority)) {
                Node<T> temp = heap.get(index);
                heap.set(index, heap.get(parent));
                heap.set(parent, temp);
                index = parent;
            } else {
                break;
            }
        }
    }

    private void siftDown(int index) {
        int size = heap.size();
        while (true) {
            int best = index;
            int left = 2 * index + 1;
            int right = 2 * index + 2;

            if (left < size && hasPrecedence(heap.get(left).priority, heap.get(best).priority)) {
                best = left;
            }
            if (right < size && hasPrecedence(heap.get(right).priority, heap.get(best).priority)) {
                best = right;
            }

            if (best != index) {
                Node<T> temp = heap.get(index);
                heap.set(index, heap.get(best));
                heap.set(best, temp);
                index = best;
            } else {
                break;
            }
        }
    }
}

