"""
Binary Search & Bound Locator

Traditional DSA Category: Searching and Sorting
Real-World Engineering Use Cases:
 - Database Optimization (indexed in-memory table lookup)
 - Search and Filtering (log timestamp range queries)
 - Financial and Payment Systems (fee tiers, discount brackets, tax schedules)

Time Complexity:
 - find: O(log N)
 - lower_bound: O(log N)
 - upper_bound: O(log N)
Space Complexity: O(1)
"""

from typing import List, Any, Optional, Callable, Dict


class BinarySearch:
    @staticmethod
    def find(
        arr: List[Any],
        target: Any,
        key: Optional[Callable[[Any], Any]] = None
    ) -> int:
        """
        Locates the exact index of target in a sorted list.
        Returns -1 if target is not found.
        """
        low = 0
        high = len(arr) - 1

        while low <= high:
            mid = low + (high - low) // 2
            val = key(arr[mid]) if key else arr[mid]

            if val == target:
                return mid
            elif val < target:
                low = mid + 1
            else:
                high = mid - 1

        return -1

    @staticmethod
    def lower_bound(
        arr: List[Any],
        target: Any,
        key: Optional[Callable[[Any], Any]] = None
    ) -> int:
        """
        Returns the first index where arr[index] >= target.
        If all elements are smaller, returns len(arr).
        """
        low = 0
        high = len(arr)

        while low < high:
            mid = low + (high - low) // 2
            val = key(arr[mid]) if key else arr[mid]

            if val >= target:
                high = mid
            else:
                low = mid + 1

        return low

    @staticmethod
    def upper_bound(
        arr: List[Any],
        target: Any,
        key: Optional[Callable[[Any], Any]] = None
    ) -> int:
        """
        Returns the first index where arr[index] > target.
        If no elements are strictly greater, returns len(arr).
        """
        low = 0
        high = len(arr)

        while low < high:
            mid = low + (high - low) // 2
            val = key(arr[mid]) if key else arr[mid]

            if val > target:
                high = mid
            else:
                low = mid + 1

        return low

    @staticmethod
    def find_tier(
        sorted_tiers: List[Dict[str, Any]],
        value: float,
        threshold_key: str = "threshold",
        value_key: str = "value"
    ) -> Optional[Any]:
        """
        Finds the highest tier threshold that is <= value.
        Used extensively in billing, commission, and recharge calculations.
        """
        if not sorted_tiers:
            return None

        low = 0
        high = len(sorted_tiers) - 1
        best_index = -1

        while low <= high:
            mid = low + (high - low) // 2
            if sorted_tiers[mid][threshold_key] <= value:
                best_index = mid
                low = mid + 1  # Seek higher matching threshold
            else:
                high = mid - 1

        return sorted_tiers[best_index][value_key] if best_index != -1 else None

