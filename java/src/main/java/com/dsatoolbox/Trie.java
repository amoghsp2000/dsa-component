package com.dsatoolbox;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Map;

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
 *
 * @param <V> Payload value type
 */
public class Trie<V> {

    public static class TrieNode<V> {
        public final Map<Character, TrieNode<V>> children = new HashMap<>();
        public boolean isEndOfWord = false;
        public V value = null;
    }

    public static class Entry<V> {
        public final String word;
        public final V value;

        public Entry(String word, V value) {
            this.word = word;
            this.value = value;
        }
    }

    private final TrieNode<V> root = new TrieNode<>();
    private int wordCount = 0;

    public void insert(String word, V value) {
        if (word == null || word.isEmpty()) return;

        TrieNode<V> current = root;
        for (int i = 0; i < word.length(); i++) {
            char ch = word.charAt(i);
            current = current.children.computeIfAbsent(ch, k -> new TrieNode<>());
        }

        if (!current.isEndOfWord) {
            wordCount++;
        }
        current.isEndOfWord = true;
        current.value = value;
    }

    public boolean search(String word) {
        TrieNode<V> node = traverseToNode(word);
        return node != null && node.isEndOfWord;
    }

    public V getValue(String word) {
        TrieNode<V> node = traverseToNode(word);
        return (node != null && node.isEndOfWord) ? node.value : null;
    }

    public boolean startsWith(String prefix) {
        return traverseToNode(prefix) != null;
    }

    public List<Entry<V>> findWordsWithPrefix(String prefix, int limit) {
        TrieNode<V> startNode = traverseToNode(prefix);
        List<Entry<V>> results = new ArrayList<>();
        if (startNode == null) return results;

        collectWords(startNode, new StringBuilder(prefix), results, limit);
        return results;
    }

    public boolean delete(String word) {
        if (word == null || word.isEmpty()) return false;
        boolean[] found = new boolean[]{false};
        deleteHelper(root, word, 0, found);
        if (found[0]) {
            wordCount--;
        }
        return found[0];
    }

    public int size() {
        return wordCount;
    }

    private TrieNode<V> traverseToNode(String prefix) {
        if (prefix == null) return null;
        TrieNode<V> current = root;
        for (int i = 0; i < prefix.length(); i++) {
            char ch = prefix.charAt(i);
            current = current.children.get(ch);
            if (current == null) return null;
        }
        return current;
    }

    private void collectWords(TrieNode<V> node, StringBuilder currentWord, List<Entry<V>> results, int limit) {
        if (results.size() >= limit) return;

        if (node.isEndOfWord) {
            results.add(new Entry<>(currentWord.toString(), node.value));
        }

        for (Map.Entry<Character, TrieNode<V>> child : node.children.entrySet()) {
            currentWord.append(child.getKey());
            collectWords(child.getValue(), currentWord, results, limit);
            currentWord.deleteCharAt(currentWord.length() - 1);
            if (results.size() >= limit) return;
        }
    }

    private boolean deleteHelper(TrieNode<V> current, String word, int index, boolean[] found) {
        if (index == word.length()) {
            if (!current.isEndOfWord) return false;
            current.isEndOfWord = false;
            current.value = null;
            found[0] = true;
            return current.children.isEmpty();
        }

        char ch = word.charAt(index);
        TrieNode<V> child = current.children.get(ch);
        if (child == null) return false;

        boolean shouldDeleteChild = deleteHelper(child, word, index + 1, found);

        if (shouldDeleteChild) {
            current.children.remove(ch);
            return current.children.isEmpty() && !current.isEndOfWord;
        }

        return false;
    }
}

