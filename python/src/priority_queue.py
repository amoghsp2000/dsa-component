"""
Priority Queue (Min / Max Binary Heap)

Traditional DSA Category: Trees and Graphs
Real-World Engineering Use Cases:
 - Queue and Background Job Processing (urgent tasks & VIP transactions)
 - Scheduling and Task Management (earliest scheduled execution timestamp)
 - Real-Time Data Processing (top-K elements, stream prioritization)

Time Complexity:
 - push: O(log N)
 - pop: O(log N)
 - peek: O(1)
 - len: O(1)
Space Complexity: O(N)
"""

from typing import Generic, TypeVar, List, Tuple, Any, Optional

T = TypeVar("T")


class PriorityQueue(Generic[T]):
    def __init__(self, is_min_heap: bool = True) -> None:
        """
        :param is_min_heap: When True, smaller numbers have higher priority (e.g. earliest timestamp).
                            When False, larger numbers have higher priority (e.g. VIP billing score).
        """
        self._heap: List[Tuple[float, T]] = []
        self._is_min_heap: bool = is_min_heap

    def push(self, value: T, priority: float) -> None:
        """Pushes an element with an associated numerical priority."""
        self._heap.append((priority, value))
        self._bubble_up(len(self._heap) - 1)

    def pop(self) -> T:
        """Extracts and returns the element with highest priority."""
        if not self._heap:
            raise IndexError("Cannot pop from an empty PriorityQueue")

        root_val = self._heap[0][1]
        last = self._heap.pop()

        if self._heap:
            self._heap[0] = last
            self._sift_down(0)

        return root_val

    def peek(self) -> T:
        """Returns the highest priority element without removing it."""
        if not self._heap:
            raise IndexError("Cannot peek into an empty PriorityQueue")
        return self._heap[0][1]

    def peek_priority(self) -> float:
        """Returns the numerical priority value of the root element."""
        if not self._heap:
            raise IndexError("Cannot peek priority of an empty PriorityQueue")
        return self._heap[0][0]

    def clear(self) -> None:
        self._heap.clear()

    def is_empty(self) -> bool:
        return len(self._heap) == 0

    def __len__(self) -> int:
        return len(self._heap)

    def _has_precedence(self, p1: float, p2: float) -> bool:
        return (p1 < p2) if self._is_min_heap else (p1 > p2)

    def _bubble_up(self, index: int) -> None:
        while index > 0:
            parent = (index - 1) // 2
            if self._has_precedence(self._heap[index][0], self._heap[parent][0]):
                self._heap[index], self._heap[parent] = self._heap[parent], self._heap[index]
                index = parent
            else:
                break

    def _sift_down(self, index: int) -> None:
        size = len(self._heap)
        while True:
            best = index
            left = 2 * index + 1
            right = 2 * index + 2

            if left < size and self._has_precedence(self._heap[left][0], self._heap[best][0]):
                best = left
            if right < size and self._has_precedence(self._heap[right][0], self._heap[best][0]):
                best = right

            if best != index:
                self._heap[index], self._heap[best] = self._heap[best], self._heap[index]
                index = best
            else:
                break

