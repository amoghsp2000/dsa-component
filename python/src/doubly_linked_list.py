"""
Doubly Linked List

Traditional DSA Category: Linked Lists
Real-World Engineering Use Cases:
 - Caching and Memory Management (backbone for LRU / LFU cache eviction chains)
 - Queue and Background Job Processing (bidirectional worker buffers)

Time Complexity:
 - prepend: O(1)
 - append: O(1)
 - remove_node: O(1) given direct node reference
 - pop_first: O(1)
 - pop_last: O(1)
Space Complexity: O(N) where N is number of nodes
"""

from typing import Generic, TypeVar, Optional, List, Any

T = TypeVar("T")


class DoublyLinkedListNode(Generic[T]):
    __slots__ = ("value", "prev", "next")

    def __init__(self, value: T):
        self.value: T = value
        self.prev: Optional["DoublyLinkedListNode[T]"] = None
        self.next: Optional["DoublyLinkedListNode[T]"] = None


class DoublyLinkedList(Generic[T]):
    def __init__(self) -> None:
        self.head: Optional[DoublyLinkedListNode[T]] = None
        self.tail: Optional[DoublyLinkedListNode[T]] = None
        self._size: int = 0

    def prepend(self, value: T) -> DoublyLinkedListNode[T]:
        """Prepends a value to the head of the list in O(1) time."""
        node = DoublyLinkedListNode(value)
        if self.head is None:
            self.head = node
            self.tail = node
        else:
            node.next = self.head
            self.head.prev = node
            self.head = node
        self._size += 1
        return node

    def append(self, value: T) -> DoublyLinkedListNode[T]:
        """Appends a value to the tail of the list in O(1) time."""
        node = DoublyLinkedListNode(value)
        if self.tail is None:
            self.head = node
            self.tail = node
        else:
            node.prev = self.tail
            self.tail.next = node
            self.tail = node
        self._size += 1
        return node

    def remove_node(self, node: DoublyLinkedListNode[T]) -> None:
        """Removes an arbitrary node in O(1) time given its direct reference."""
        if node.prev is not None:
            node.prev.next = node.next
        else:
            self.head = node.next

        if node.next is not None:
            node.next.prev = node.prev
        else:
            self.tail = node.prev

        node.prev = None
        node.next = None
        self._size -= 1

    def pop_first(self) -> Optional[T]:
        """Removes and returns the value at the head of the list."""
        if self.head is None:
            return None
        val = self.head.value
        self.remove_node(self.head)
        return val

    def pop_last(self) -> Optional[T]:
        """Removes and returns the value at the tail of the list."""
        if self.tail is None:
            return None
        val = self.tail.value
        self.remove_node(self.tail)
        return val

    def to_list(self) -> List[T]:
        """Converts the list to a Python list."""
        result: List[T] = []
        current = self.head
        while current is not None:
            result.append(current.value)
            current = current.next
        return result

    def clear(self) -> None:
        """Clears all nodes."""
        current = self.head
        while current is not None:
            next_node = current.next
            current.prev = None
            current.next = None
            current = next_node
        self.head = None
        self.tail = None
        self._size = 0

    def __len__(self) -> int:
        return self._size

    def is_empty(self) -> bool:
        return self._size == 0

