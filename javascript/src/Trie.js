/**
 * Trie Node representing a single character step.
 */
class TrieNode {
  constructor() {
    this.children = new Map();
    this.isEndOfWord = false;
    this.value = null;
  }
}

/**
 * Trie (Prefix Tree)
 *
 * Traditional DSA Category: Trees and Graphs
 * Real-World Engineering Use Cases:
 *  - Search and Filtering (instantaneous autocomplete, suggestion ranking)
 *  - API and HTTP Processing (sub-millisecond path routing and URL prefix dispatch)
 *  - Networking and Routing (longest prefix match for CIDR blocks or phone codes)
 *
 * Time Complexity:
 *  - insert: O(M) where M is key length
 *  - search: O(M)
 *  - startsWith: O(M)
 *  - delete: O(M)
 * Space Complexity: O(ALPHABET_SIZE * M * N)
 */
class Trie {
  constructor() {
    this.root = new TrieNode();
    this.wordCount = 0;
  }

  insert(word, value = null) {
    if (!word) return;

    let current = this.root;
    for (let i = 0; i < word.length; i++) {
      const char = word[i];
      if (!current.children.has(char)) {
        current.children.set(char, new TrieNode());
      }
      current = current.children.get(char);
    }

    if (!current.isEndOfWord) {
      this.wordCount++;
    }
    current.isEndOfWord = true;
    current.value = value;
  }

  search(word) {
    const node = this._traverseToNode(word);
    return node !== null && node.isEndOfWord;
  }

  getValue(word) {
    const node = this._traverseToNode(word);
    return node !== null && node.isEndOfWord ? node.value : null;
  }

  startsWith(prefix) {
    return this._traverseToNode(prefix) !== null;
  }

  findWordsWithPrefix(prefix, limit = 50) {
    const startNode = this._traverseToNode(prefix);
    if (!startNode) return [];

    const results = [];
    this._collectWords(startNode, prefix, results, limit);
    return results;
  }

  delete(word) {
    if (!word) return false;
    const found = { val: false };

    const deleteHelper = (current, str, index) => {
      if (index === str.length) {
        if (!current.isEndOfWord) return false;
        current.isEndOfWord = false;
        current.value = null;
        found.val = true;
        return current.children.size === 0;
      }

      const char = str[index];
      if (!current.children.has(char)) return false;

      const shouldDeleteChild = deleteHelper(current.children.get(char), str, index + 1);

      if (shouldDeleteChild) {
        current.children.delete(char);
        return current.children.size === 0 && !current.isEndOfWord;
      }

      return false;
    };

    deleteHelper(this.root, word, 0);
    if (found.val) {
      this.wordCount--;
    }
    return found.val;
  }

  get size() {
    return this.wordCount;
  }

  _traverseToNode(prefix) {
    let current = this.root;
    for (let i = 0; i < prefix.length; i++) {
      const char = prefix[i];
      if (!current.children.has(char)) return null;
      current = current.children.get(char);
    }
    return current;
  }

  _collectWords(node, currentWord, results, limit) {
    if (results.length >= limit) return;

    if (node.isEndOfWord) {
      results.push({ word: currentWord, value: node.value });
    }

    for (const [char, child] of node.children.entries()) {
      this._collectWords(child, currentWord + char, results, limit);
      if (results.length >= limit) return;
    }
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { Trie, TrieNode };
}

