"""
Prefix Sum & Range Query Engine

Traditional DSA Category: Arrays and Strings
Real-World Engineering Use Cases:
 - Financial and Payment Systems (ledger balance calculation, revenue intervals)
 - Logging and Monitoring (sub-millisecond metric aggregation over sliding intervals)
 - Performance Optimization (replacing linear scans with O(1) difference lookups)

Time Complexity:
 - Build 1D: O(N)
 - Query 1D: O(1)
 - Build 2D: O(R * C)
 - Query 2D: O(1)
Space Complexity:
 - 1D: O(N)
 - 2D: O(R * C)
"""

from typing import List, Union, Optional

Number = Union[int, float]


class PrefixSum:
    def __init__(self, values: Optional[List[Number]] = None) -> None:
        self._prefix1d: List[Number] = []
        self._length1d: int = 0

        self._prefix2d: List[List[Number]] = []
        self._rows: int = 0
        self._cols: int = 0

        if values:
            self.build_1d(values)

    def build_1d(self, values: List[Number]) -> None:
        """Builds the 1D prefix sum array."""
        self._length1d = len(values)
        self._prefix1d = [0] * (self._length1d + 1)
        for i, val in enumerate(values):
            self._prefix1d[i + 1] = self._prefix1d[i] + val

    def query_range(self, left: int, right: int) -> Number:
        """
        Queries the cumulative sum between indices left and right (inclusive) in O(1) time.
        """
        if left < 0 or right >= self._length1d:
            raise IndexError(f"Indices [{left}, {right}] out of bounds [0, {self._length1d - 1}]")
        if left > right:
            raise ValueError(f"Left index ({left}) cannot be greater than right index ({right})")

        return self._prefix1d[right + 1] - self._prefix1d[left]

    def build_2d(self, matrix: List[List[Number]]) -> None:
        """Builds the 2D prefix sum table for grid-based interval calculations."""
        self._rows = len(matrix)
        self._cols = len(matrix[0]) if self._rows > 0 else 0

        if self._rows == 0 or self._cols == 0:
            self._prefix2d = []
            return

        self._prefix2d = [[0] * (self._cols + 1) for _ in range(self._rows + 1)]

        for r in range(self._rows):
            for c in range(self._cols):
                self._prefix2d[r + 1][c + 1] = (
                    matrix[r][c]
                    + self._prefix2d[r][c + 1]
                    + self._prefix2d[r + 1][c]
                    - self._prefix2d[r][c]
                )

    def query_2d(self, r1: int, c1: int, r2: int, c2: int) -> Number:
        """
        Queries the subgrid sum from (r1, c1) to (r2, c2) inclusive in O(1) time.
        """
        if r1 < 0 or r2 >= self._rows or c1 < 0 or c2 >= self._cols:
            raise IndexError("Subgrid indices out of bounds")
        if r1 > r2 or c1 > c2:
            raise ValueError("Invalid subgrid boundaries: top-left must precede bottom-right")

        return (
            self._prefix2d[r2 + 1][c2 + 1]
            - self._prefix2d[r1][c2 + 1]
            - self._prefix2d[r2 + 1][c1]
            + self._prefix2d[r1][c1]
        )

    def __len__(self) -> int:
        return self._length1d
