package com.dsatoolbox;

import java.util.Comparator;
import java.util.List;

/**
 * Binary Search & Bound Locator
 *
 * Traditional DSA Category: Searching and Sorting
 * Real-World Engineering Use Cases:
 *  - Database Optimization (indexed in-memory table lookup)
 *  - Search and Filtering (log timestamp range queries)
 *  - Financial and Payment Systems (fee tiers, discount brackets, tax schedules)
 *
 * Time Complexity:
 *  - find: O(log N)
 *  - lowerBound: O(log N)
 *  - upperBound: O(log N)
 * Space Complexity: O(1)
 */
public class BinarySearch {

    public static <T extends Comparable<T>> int find(List<T> list, T target) {
        return find(list, target, Comparator.naturalOrder());
    }

    public static <T> int find(List<T> list, T target, Comparator<T> comparator) {
        int low = 0;
        int high = list.size() - 1;

        while (low <= high) {
            int mid = low + (high - low) / 2;
            int cmp = comparator.compare(list.get(mid), target);

            if (cmp == 0) return mid;
            if (cmp < 0) {
                low = mid + 1;
            } else {
                high = mid - 1;
            }
        }

        return -1;
    }

    public static <T extends Comparable<T>> int lowerBound(List<T> list, T target) {
        return lowerBound(list, target, Comparator.naturalOrder());
    }

    public static <T> int lowerBound(List<T> list, T target, Comparator<T> comparator) {
        int low = 0;
        int high = list.size();

        while (low < high) {
            int mid = low + (high - low) / 2;
            int cmp = comparator.compare(list.get(mid), target);

            if (cmp >= 0) {
                high = mid;
            } else {
                low = mid + 1;
            }
        }

        return low;
    }

    public static <T extends Comparable<T>> int upperBound(List<T> list, T target) {
        return upperBound(list, target, Comparator.naturalOrder());
    }

    public static <T> int upperBound(List<T> list, T target, Comparator<T> comparator) {
        int low = 0;
        int high = list.size();

        while (low < high) {
            int mid = low + (high - low) / 2;
            int cmp = comparator.compare(list.get(mid), target);

            if (cmp > 0) {
                high = mid;
            } else {
                low = mid + 1;
            }
        }

        return low;
    }

    public static class Tier<V> {
        public final double threshold;
        public final V value;

        public Tier(double threshold, V value) {
            this.threshold = threshold;
            this.value = value;
        }
    }

    public static <V> V findTier(List<Tier<V>> sortedTiers, double value) {
        if (sortedTiers == null || sortedTiers.isEmpty()) return null;

        int low = 0;
        int high = sortedTiers.size() - 1;
        int bestIndex = -1;

        while (low <= high) {
            int mid = low + (high - low) / 2;
            if (sortedTiers.get(mid).threshold <= value) {
                bestIndex = mid;
                low = mid + 1;
            } else {
                high = mid - 1;
            }
        }

        return bestIndex != -1 ? sortedTiers.get(bestIndex).value : null;
    }
}

