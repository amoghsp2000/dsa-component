<?php

declare(strict_types=1);

namespace DsaToolbox;

use OutOfBoundsException;
use InvalidArgumentException;

/**
 * Prefix Sum & Range Query Engine
 *
 * Traditional DSA Category: Arrays and Strings
 * Real-World Engineering Use Cases:
 *  - Financial and Payment Systems (ledger balance calculation, revenue intervals)
 *  - Logging and Monitoring (sub-millisecond metric aggregation over sliding intervals)
 *  - Performance Optimization (replacing linear scans with O(1) difference lookups)
 *
 * Time Complexity:
 *  - Build 1D: O(N)
 *  - Query 1D: O(1)
 *  - Build 2D: O(R * C)
 *  - Query 2D: O(1)
 * Space Complexity:
 *  - 1D: O(N)
 *  - 2D: O(R * C)
 */
class PrefixSum
{
    /** @var array<int, int|float> 1-indexed prefix sums */
    private array $prefix1D = [];
    private int $length1D = 0;

    /** @var array<int, array<int, int|float>> 2D prefix sum grid */
    private array $prefix2D = [];
    private int $rows = 0;
    private int $cols = 0;

    /**
     * Initializes a 1D prefix sum engine.
     *
     * @param array<int, int|float> $values Array of numeric values
     */
    public function __construct(array $values = [])
    {
        if (!empty($values)) {
            $this->build1D($values);
        }
    }

    /**
     * Builds or rebuilds the 1D prefix sum table.
     *
     * @param array<int, int|float> $values
     */
    public function build1D(array $values): void
    {
        $this->length1D = count($values);
        $this->prefix1D = array_fill(0, $this->length1D + 1, 0);

        for ($i = 0; $i < $this->length1D; $i++) {
            $this->prefix1D[$i + 1] = $this->prefix1D[$i] + $values[$i];
        }
    }

    /**
     * Queries the cumulative sum between indices $left and $right (inclusive) in O(1) time.
     *
     * @param int $left Zero-based inclusive start index
     * @param int $right Zero-based inclusive end index
     * @return int|float Cumulative sum
     * @throws OutOfBoundsException If indices are outside valid array bounds
     * @throws InvalidArgumentException If $left > $right
     */
    public function queryRange(int $left, int $right): int|float
    {
        if ($left < 0 || $right >= $this->length1D) {
            throw new OutOfBoundsException("Indices [$left, $right] out of bounds [0, " . ($this->length1D - 1) . "]");
        }
        if ($left > $right) {
            throw new InvalidArgumentException("Left index ($left) cannot be greater than right index ($right)");
        }

        return $this->prefix1D[$right + 1] - $this->prefix1D[$left];
    }

    /**
     * Builds a 2D prefix sum matrix for grid-based calculations.
     *
     * @param array<int, array<int, int|float>> $matrix
     */
    public function build2D(array $matrix): void
    {
        $this->rows = count($matrix);
        $this->cols = $this->rows > 0 ? count($matrix[0]) : 0;

        if ($this->rows === 0 || $this->cols === 0) {
            $this->prefix2D = [];
            return;
        }

        // Initialize (rows + 1) x (cols + 1) matrix with zeros
        $this->prefix2D = array_fill(0, $this->rows + 1, array_fill(0, $this->cols + 1, 0));

        for ($r = 0; $r < $this->rows; $r++) {
            for ($c = 0; $c < $this->cols; $c++) {
                $this->prefix2D[$r + 1][$c + 1] = $matrix[$r][$c]
                    + $this->prefix2D[$r][$c + 1]
                    + $this->prefix2D[$r + 1][$c]
                    - $this->prefix2D[$r][$c];
            }
        }
    }

    /**
     * Queries the subgrid sum from ($r1, $c1) to ($r2, $c2) inclusive in O(1) time.
     *
     * @param int $r1 Top-left row
     * @param int $c1 Top-left col
     * @param int $r2 Bottom-right row
     * @param int $c2 Bottom-right col
     * @return int|float
     */
    public function query2D(int $r1, int $c1, int $r2, int $c2): int|float
    {
        if ($r1 < 0 || $r2 >= $this->rows || $c1 < 0 || $c2 >= $this->cols) {
            throw new OutOfBoundsException("Subgrid indices out of bounds");
        }
        if ($r1 > $r2 || $c1 > $c2) {
            throw new InvalidArgumentException("Invalid subgrid boundaries");
        }

        return $this->prefix2D[$r2 + 1][$c2 + 1]
            - $this->prefix2D[$r1][$c2 + 1]
            - $this->prefix2D[$r2 + 1][$c1]
            + $this->prefix2D[$r1][$c1];
    }

    /**
     * Returns total element count in 1D array.
     */
    public function count(): int
    {
        return $this->length1D;
    }
}

