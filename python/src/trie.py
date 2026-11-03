"""
Trie (Prefix Tree)

Traditional DSA Category: Trees and Graphs
Real-World Engineering Use Cases:
 - Search and Filtering (instantaneous autocomplete, suggestion ranking)
 - API and HTTP Processing (sub-millisecond path routing and URL prefix dispatch)
 - Networking and Routing (longest prefix match for CIDR blocks or phone codes)

Time Complexity:
 - insert: O(M) where M is key length
 - search: O(M)
 - starts_with: O(M)
 - delete: O(M)
Space Complexity: O(ALPHABET_SIZE * M * N)
"""

from typing import Dict, Optional, Any, List


class TrieNode:
    __slots__ = ("children", "is_end_of_word", "value")

    def __init__(self) -> None:
        self.children: Dict[str, "TrieNode"] = {}
        self.is_end_of_word: bool = False
        self.value: Optional[Any] = None


class Trie:
    def __init__(self) -> None:
        self.root: TrieNode = TrieNode()
        self._word_count: int = 0

    def insert(self, word: str, value: Optional[Any] = None) -> None:
        """Inserts a word and an optional payload into the trie."""
        if not word:
            return

        current = self.root
        for char in word:
            if char not in current.children:
                current.children[char] = TrieNode()
            current = current.children[char]

        if not current.is_end_of_word:
            self._word_count += 1
        current.is_end_of_word = True
        current.value = value

    def search(self, word: str) -> bool:
        """Checks if an exact word exists in the trie."""
        node = self._traverse_to_node(word)
        return node is not None and node.is_end_of_word

    def get_value(self, word: str) -> Optional[Any]:
        """Retrieves payload associated with an exact word."""
        node = self._traverse_to_node(word)
        return node.value if (node is not None and node.is_end_of_word) else None

    def starts_with(self, prefix: str) -> bool:
        """Checks if any registered word starts with the given prefix."""
        return self._traverse_to_node(prefix) is not None

    def find_words_with_prefix(self, prefix: str, limit: int = 50) -> List[Dict[str, Any]]:
        """
        Retrieves all words and payloads matching prefix, up to limit.
        Ideal for real-time search autocompletion.
        """
        start_node = self._traverse_to_node(prefix)
        if start_node is None:
            return []

        results: List[Dict[str, Any]] = []
        self._collect_words(start_node, prefix, results, limit)
        return results

    def delete(self, word: str) -> bool:
        """Deletes a word from the trie."""
        if not word:
            return False

        found = [False]

        def _delete_helper(current: TrieNode, w: str, index: int) -> bool:
            if index == len(w):
                if not current.is_end_of_word:
                    return False
                current.is_end_of_word = False
                current.value = None
                found[0] = True
                return len(current.children) == 0

            char = w[index]
            if char not in current.children:
                return False

            should_delete_child = _delete_helper(current.children[char], w, index + 1)

            if should_delete_child:
                del current.children[char]
                return len(current.children) == 0 and not current.is_end_of_word

            return False

        _delete_helper(self.root, word, 0)
        if found[0]:
            self._word_count -= 1
        return found[0]

    def _traverse_to_node(self, prefix: str) -> Optional[TrieNode]:
        current = self.root
        for char in prefix:
            if char not in current.children:
                return None
            current = current.children[char]
        return current

    def _collect_words(
        self, node: TrieNode, current_word: str, results: List[Dict[str, Any]], limit: int
    ) -> None:
        if len(results) >= limit:
            return

        if node.is_end_of_word:
            results.append({"word": current_word, "value": node.value})

        for char, child in node.children.items():
            self._collect_words(child, current_word + char, results, limit)
            if len(results) >= limit:
                return

    def __len__(self) -> int:
        return self._word_count

