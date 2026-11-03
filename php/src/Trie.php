<?php

declare(strict_types=1);

namespace DsaToolbox;

/**
 * Trie Node representing a single character step.
 */
class TrieNode
{
    /** @var array<string, TrieNode> Map from character to child node */
    public array $children = [];

    /** @var bool Whether this node terminates a complete stored word */
    public bool $isEndOfWord = false;

    /** @var mixed Optional payload attached to the word */
    public mixed $value = null;
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
 *  - insert: O(M) where M is string length
 *  - search: O(M)
 *  - startsWith: O(M)
 *  - delete: O(M)
 * Space Complexity: O(ALPHABET_SIZE * M * N)
 */
class Trie
{
    private TrieNode $root;
    private int $wordCount = 0;

    public function __construct()
    {
        $this->root = new TrieNode();
    }

    /**
     * Inserts a word with an optional payload into the trie.
     */
    public function insert(string $word, mixed $value = null): void
    {
        if ($word === '') {
            return;
        }

        $current = $this->root;
        $len = strlen($word);

        for ($i = 0; $i < $len; $i++) {
            $char = $word[$i];
            if (!isset($current->children[$char])) {
                $current->children[$char] = new TrieNode();
            }
            $current = $current->children[$char];
        }

        if (!$current->isEndOfWord) {
            $this->wordCount++;
        }
        $current->isEndOfWord = true;
        $current->value = $value;
    }

    /**
     * Checks if an exact word exists in the trie.
     */
    public function search(string $word): bool
    {
        $node = $this->traverseToNode($word);
        return $node !== null && $node->isEndOfWord;
    }

    /**
     * Retrieves the payload attached to an exact word, or null if missing.
     */
    public function getValue(string $word): mixed
    {
        $node = $this->traverseToNode($word);
        return ($node !== null && $node->isEndOfWord) ? $node->value : null;
    }

    /**
     * Checks if any registered word starts with the given prefix.
     */
    public function startsWith(string $prefix): bool
    {
        return $this->traverseToNode($prefix) !== null;
    }

    /**
     * Retrieves all words (with optional payloads) starting with $prefix up to $limit.
     * Ideal for real-time search autocompletion.
     *
     * @param string $prefix Search prefix
     * @param int $limit Maximum results to return
     * @return array<int, array{word: string, value: mixed}>
     */
    public function findWordsWithPrefix(string $prefix, int $limit = 50): array
    {
        $startNode = $this->traverseToNode($prefix);
        if ($startNode === null) {
            return [];
        }

        $results = [];
        $this->collectWords($startNode, $prefix, $results, $limit);
        return $results;
    }

    /**
     * Deletes a word from the trie.
     *
     * @return bool True if word was found and deleted, false otherwise
     */
    public function delete(string $word): bool
    {
        if ($word === '') {
            return false;
        }
        $found = false;
        $this->deleteHelper($this->root, $word, 0, $found);
        if ($found) {
            $this->wordCount--;
        }
        return $found;
    }

    public function count(): int
    {
        return $this->wordCount;
    }

    private function traverseToNode(string $prefix): ?TrieNode
    {
        $current = $this->root;
        $len = strlen($prefix);

        for ($i = 0; $i < $len; $i++) {
            $char = $prefix[$i];
            if (!isset($current->children[$char])) {
                return null;
            }
            $current = $current->children[$char];
        }

        return $current;
    }

    /**
     * @param array<int, array{word: string, value: mixed}> $results
     */
    private function collectWords(TrieNode $node, string $currentWord, array &$results, int $limit): void
    {
        if (count($results) >= $limit) {
            return;
        }

        if ($node->isEndOfWord) {
            $results[] = [
                'word' => $currentWord,
                'value' => $node->value
            ];
        }

        foreach ($node->children as $char => $childNode) {
            $this->collectWords($childNode, $currentWord . $char, $results, $limit);
            if (count($results) >= $limit) {
                return;
            }
        }
    }

    private function deleteHelper(TrieNode $current, string $word, int $index, bool &$found): bool
    {
        if ($index === strlen($word)) {
            if (!$current->isEndOfWord) {
                return false;
            }
            $current->isEndOfWord = false;
            $current->value = null;
            $found = true;
            return empty($current->children);
        }

        $char = $word[$index];
        if (!isset($current->children[$char])) {
            return false;
        }

        $shouldDeleteChild = $this->deleteHelper($current->children[$char], $word, $index + 1, $found);

        if ($shouldDeleteChild) {
            unset($current->children[$char]);
            return empty($current->children) && !$current->isEndOfWord;
        }

        return false;
    }
}

