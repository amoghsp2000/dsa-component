<?php

declare(strict_types=1);

namespace DsaToolbox;

/**
 * DoublyLinkedList Node.
 *
 * @template T
 */
class DoublyLinkedListNode
{
    /** @var T */
    public mixed $value;
    public ?DoublyLinkedListNode $prev = null;
    public ?DoublyLinkedListNode $next = null;

    /**
     * @param T $value
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }
}

/**
 * Doubly Linked List
 *
 * Traditional DSA Category: Linked Lists
 * Real-World Engineering Use Cases:
 *  - Caching and Memory Management (backbone for LRU / LFU eviction chains)
 *  - Queue and Background Job Processing (bidirectional worker buffers)
 *
 * Time Complexity:
 *  - prepend: O(1)
 *  - append: O(1)
 *  - removeNode: O(1) given direct node reference
 *  - popFirst: O(1)
 *  - popLast: O(1)
 * Space Complexity: O(N) where N is the number of nodes
 *
 * @template T
 */
class DoublyLinkedList
{
    /** @var DoublyLinkedListNode<T>|null */
    private ?DoublyLinkedListNode $head = null;

    /** @var DoublyLinkedListNode<T>|null */
    private ?DoublyLinkedListNode $tail = null;

    private int $size = 0;

    /**
     * Prepends a value to the head of the list.
     *
     * @param T $value
     * @return DoublyLinkedListNode<T> The newly created node
     */
    public function prepend(mixed $value): DoublyLinkedListNode
    {
        $node = new DoublyLinkedListNode($value);
        if ($this->head === null) {
            $this->head = $node;
            $this->tail = $node;
        } else {
            $node->next = $this->head;
            $this->head->prev = $node;
            $this->head = $node;
        }
        $this->size++;
        return $node;
    }

    /**
     * Appends a value to the tail of the list.
     *
     * @param T $value
     * @return DoublyLinkedListNode<T> The newly created node
     */
    public function append(mixed $value): DoublyLinkedListNode
    {
        $node = new DoublyLinkedListNode($value);
        if ($this->tail === null) {
            $this->head = $node;
            $this->tail = $node;
        } else {
            $node->prev = $this->tail;
            $this->tail->next = $node;
            $this->tail = $node;
        }
        $this->size++;
        return $node;
    }

    /**
     * Removes an arbitrary node in O(1) time.
     *
     * @param DoublyLinkedListNode<T> $node
     */
    public function removeNode(DoublyLinkedListNode $node): void
    {
        if ($node->prev !== null) {
            $node->prev->next = $node->next;
        } else {
            $this->head = $node->next;
        }

        if ($node->next !== null) {
            $node->next->prev = $node->prev;
        } else {
            $this->tail = $node->prev;
        }

        $node->prev = null;
        $node->next = null;
        $this->size--;
    }

    /**
     * Removes and returns the value at the head.
     *
     * @return T|null
     */
    public function popFirst(): mixed
    {
        if ($this->head === null) {
            return null;
        }
        $val = $this->head->value;
        $this->removeNode($this->head);
        return $val;
    }

    /**
     * Removes and returns the value at the tail.
     *
     * @return T|null
     */
    public function popLast(): mixed
    {
        if ($this->tail === null) {
            return null;
        }
        $val = $this->tail->value;
        $this->removeNode($this->tail);
        return $val;
    }

    /**
     * Returns the head node without removing it.
     *
     * @return DoublyLinkedListNode<T>|null
     */
    public function getHead(): ?DoublyLinkedListNode
    {
        return $this->head;
    }

    /**
     * Returns the tail node without removing it.
     *
     * @return DoublyLinkedListNode<T>|null
     */
    public function getTail(): ?DoublyLinkedListNode
    {
        return $this->tail;
    }

    /**
     * Returns total element count.
     */
    public function count(): int
    {
        return $this->size;
    }

    public function isEmpty(): bool
    {
        return $this->size === 0;
    }

    /**
     * Converts the list to a standard PHP array.
     *
     * @return array<int, T>
     */
    public function toArray(): array
    {
        $result = [];
        $current = $this->head;
        while ($current !== null) {
            $result[] = $current->value;
            $current = $current->next;
        }
        return $result;
    }

    /**
     * Clears all nodes.
     */
    public function clear(): void
    {
        $current = $this->head;
        while ($current !== null) {
            $next = $current->next;
            $current->prev = null;
            $current->next = null;
            $current = $next;
        }
        $this->head = null;
        $this->tail = null;
        $this->size = 0;
    }
}

