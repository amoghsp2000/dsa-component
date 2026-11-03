<?php

declare(strict_types=1);

namespace DsaToolbox;

use InvalidArgumentException;

/**
 * Binary Search & Bound Locator
 *
 * Traditional DSA Category: Searching and Sorting
 * Real-World Engineering Use Cases:
 *  - Database Optimization (indexed in-memory table lookup)
 *  - Search and Filtering (log timestamp slice queries)
 *  - Financial and Payment Systems (tier resolution, discount brackets, fee schedules)
 *
 * Time Complexity:
 *  - find: O(log N)
 *  - lowerBound: O(log N)
 *  - upperBound: O(log N)
 * Space Complexity: O(1)
 */
class BinarySearch
{
    /**
     * Finds the exact index of $target in a sorted array, or returns -1 if not found.
     *
     * @param array<int, int|float|string> $arr Sorted array
     * @param int|float|string $target Value to locate
     * @param callable|null $comparator Optional fn($a, $b) returning <0, 0, >0
     * @return int Index of target, or -1
     */
    public static function find(array $arr, mixed $target, ?callable $comparator = null): int
    {
        $low = 0;
        $high = count($arr) - 1;

        while ($low <= $high) {
            // Prevent 32-bit integer overflow
            $mid = $low + intdiv($high - $low, 2);
            $cmp = $comparator !== null ? $comparator($arr[$mid], $target) : ($arr[$mid] <=> $target);

            if ($cmp === 0) {
                return $mid;
            }

            if ($cmp < 0) {
                $low = $mid + 1;
            } else {
                $high = $mid - 1;
            }
        }

        return -1;
    }

    /**
     * Finds the index of the first element in a sorted array that is >= $target.
     * If all elements are smaller than $target, returns count($arr).
     *
     * @param array<int, int|float|string> $arr Sorted array
     * @param int|float|string $target
     * @param callable|null $comparator
     * @return int First index where element is >= target
     */
    public static function lowerBound(array $arr, mixed $target, ?callable $comparator = null): int
    {
        $low = 0;
        $high = count($arr);

        while ($low < $high) {
            $mid = $low + intdiv($high - $low, 2);
            $cmp = $comparator !== null ? $comparator($arr[$mid], $target) : ($arr[$mid] <=> $target);

            if ($cmp >= 0) {
                $high = $mid;
            } else {
                $low = $mid + 1;
            }
        }

        return $low;
    }

    /**
     * Finds the index of the first element in a sorted array that is strictly > $target.
     * If no elements are greater, returns count($arr).
     *
     * @param array<int, int|float|string> $arr Sorted array
     * @param int|float|string $target
     * @param callable|null $comparator
     * @return int First index where element is > target
     */
    public static function upperBound(array $arr, mixed $target, ?callable $comparator = null): int
    {
        $low = 0;
        $high = count($arr);

        while ($low < $high) {
            $mid = $low + intdiv($high - $low, 2);
            $cmp = $comparator !== null ? $comparator($arr[$mid], $target) : ($arr[$mid] <=> $target);

            if ($cmp > 0) {
                $high = $mid;
            } else {
                $low = $mid + 1;
            }
        }

        return $low;
    }

    /**
     * Resolves a tier/bracket for financial and billing systems (e.g. commission tiers, recharge brackets).
     * Finds the highest tier threshold that is <= $value.
     *
     * @param array<int, array{threshold: int|float, value: mixed}> $sortedTiers
     * @param int|float $value
     * @return mixed Value associated with the matching tier, or null if below lowest tier
     */
    public static function findTier(array $sortedTiers, int|float $value): mixed
    {
        if (empty($sortedTiers)) {
            return null;
        }

        $low = 0;
        $high = count($sortedTiers) - 1;
        $bestIndex = -1;

        while ($low <= $high) {
            $mid = $low + intdiv($high - $low, 2);
            if ($sortedTiers[$mid]['threshold'] <= $value) {
                $bestIndex = $mid;
                $low = $mid + 1; // Seek higher matching thresholds
            } else {
                $high = $mid - 1;
            }
        }

        return $bestIndex !== -1 ? $sortedTiers[$bestIndex]['value'] : null;
    }
}

