"""
Consistent Hash Ring with Virtual Nodes

Traditional DSA Category: Advanced Data Structures (Searching & Hash Tables)
Real-World Engineering Use Cases:
 - Distributed Systems (distributing cache keys across Redis / Memcached clusters)
 - Caching and Memory Management (minimizing cache misses on cluster scaling)
 - Networking and Routing (sticky session routing and gateway sharding)

Time Complexity:
 - add_node: O(V * log(N * V)) where V is virtual replicas, N is server count
 - remove_node: O(V * log(N * V))
 - get_node: O(log(N * V)) via binary search on ring
Space Complexity: O(N * V)
"""

import zlib
import bisect
from typing import Dict, List, Optional, Set


class ConsistentHash:
    def __init__(self, replicas: int = 100) -> None:
        if replicas < 1:
            raise ValueError("replicas must be at least 1")
        self.replicas: int = replicas
        self._ring: List[int] = []
        self._ring_map: Dict[int, str] = {}
        self._nodes: Set[str] = set()

    def add_node(self, node: str) -> None:
        """Adds a physical node and its virtual replicas to the hash ring."""
        if node in self._nodes:
            return

        self._nodes.add(node)

        for i in range(self.replicas):
            v_key = f"{node}#replica_{i}"
            h = self._hash(v_key)
            self._ring_map[h] = node
            bisect.insort(self._ring, h)

    def remove_node(self, node: str) -> None:
        """Removes a physical node and its virtual replicas from the hash ring."""
        if node not in self._nodes:
            return

        self._nodes.remove(node)

        for i in range(self.replicas):
            v_key = f"{node}#replica_{i}"
            h = self._hash(v_key)
            if h in self._ring_map:
                del self._ring_map[h]

        self._ring = sorted(self._ring_map.keys())

    def get_node(self, key: str) -> Optional[str]:
        """
        Finds the target physical node responsible for a key.
        Uses binary search (bisect) on the circular ring.
        """
        if not self._ring:
            return None

        h = self._hash(key)
        idx = bisect.bisect_left(self._ring, h)

        # Wrap-around
        if idx == len(self._ring):
            idx = 0

        node_hash = self._ring[idx]
        return self._ring_map.get(node_hash)

    def get_nodes(self) -> List[str]:
        """Returns all registered physical nodes."""
        return list(self._nodes)

    def __len__(self) -> int:
        return len(self._nodes)

    def _hash(self, key: str) -> int:
        return zlib.crc32(key.encode("utf-8"))

