"""
Disjoint Set Union (Union-Find) with Path Compression and Union by Rank

Traditional DSA Category: Advanced Data Structures
Real-World Engineering Use Cases:
 - Networking and Routing (mesh connectivity, cluster partition checking)
 - Distributed Systems (server group membership, partition split detection)
 - Transaction Processing (circular transaction detection, account fraud rings)

Time Complexity:
 - find: O(alpha(N)) amortized (effectively constant time)
 - union: O(alpha(N)) amortized
 - connected: O(alpha(N)) amortized
Space Complexity: O(N)
"""

from typing import Dict, Any


class UnionFind:
    def __init__(self) -> None:
        self._parent: Dict[str, str] = {}
        self._rank: Dict[str, int] = {}
        self._set_size: Dict[str, int] = {}
        self._component_count: int = 0

    def find(self, element: Any) -> str:
        """
        Finds the representative root of element with path compression.
        Automatically registers new elements as singletons.
        """
        key = str(element)

        if key not in self._parent:
            self._parent[key] = key
            self._rank[key] = 0
            self._set_size[key] = 1
            self._component_count += 1
            return key

        if self._parent[key] != key:
            self._parent[key] = self.find(self._parent[key])  # Path compression

        return self._parent[key]

    def union(self, a: Any, b: Any) -> bool:
        """
        Unites the sets containing a and b using union-by-rank.
        Returns True if sets were distinct and merged.
        Returns False if they were already in the same set (cycle detected).
        """
        root_a = self.find(a)
        root_b = self.find(b)

        if root_a == root_b:
            return False  # Already connected; cycle created

        if self._rank[root_a] < self._rank[root_b]:
            self._parent[root_a] = root_b
            self._set_size[root_b] += self._set_size[root_a]
        elif self._rank[root_a] > self._rank[root_b]:
            self._parent[root_b] = root_a
            self._set_size[root_a] += self._set_size[root_b]
        else:
            self._parent[root_b] = root_a
            self._set_size[root_a] += self._set_size[root_b]
            self._rank[root_a] += 1

        self._component_count -= 1
        return True

    def connected(self, a: Any, b: Any) -> bool:
        """Checks if two elements belong to the same connected component."""
        return self.find(a) == self.find(b)

    def get_component_size(self, element: Any) -> int:
        """Returns the size of the set containing element."""
        root = self.find(element)
        return self._set_size[root]

    @property
    def component_count(self) -> int:
        """Returns the total number of disjoint sets."""
        return self._component_count

    def __len__(self) -> int:
        """Returns the total number of registered elements."""
        return len(self._parent)

