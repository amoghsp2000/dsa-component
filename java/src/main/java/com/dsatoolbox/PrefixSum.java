package com.dsatoolbox;

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
public class PrefixSum {

    private double[] prefix1D;
    private int length1D = 0;

    private double[][] prefix2D;
    private int rows = 0;
    private int cols = 0;

    public PrefixSum() {}

    public PrefixSum(double[] values) {
        if (values != null && values.length > 0) {
            build1D(values);
        }
    }

    public void build1D(double[] values) {
        this.length1D = values.length;
        this.prefix1D = new double[length1D + 1];

        for (int i = 0; i < length1D; i++) {
            this.prefix1D[i + 1] = this.prefix1D[i] + values[i];
        }
    }

    public double queryRange(int left, int right) {
        if (left < 0 || right >= length1D) {
            throw new IndexOutOfBoundsException("Indices out of bounds: [" + left + ", " + right + "]");
        }
        if (left > right) {
            throw new IllegalArgumentException("Left index cannot exceed right index");
        }

        return prefix1D[right + 1] - prefix1D[left];
    }

    public void build2D(double[][] matrix) {
        if (matrix == null || matrix.length == 0 || matrix[0].length == 0) {
            this.prefix2D = new double[0][0];
            return;
        }

        this.rows = matrix.length;
        this.cols = matrix[0].length;
        this.prefix2D = new double[rows + 1][cols + 1];

        for (int r = 0; r < rows; r++) {
            for (int c = 0; c < cols; c++) {
                this.prefix2D[r + 1][c + 1] = matrix[r][c]
                        + this.prefix2D[r][c + 1]
                        + this.prefix2D[r + 1][c]
                        - this.prefix2D[r][c];
            }
        }
    }

    public double query2D(int r1, int c1, int r2, int c2) {
        if (r1 < 0 || r2 >= rows || c1 < 0 || c2 >= cols) {
            throw new IndexOutOfBoundsException("Subgrid indices out of bounds");
        }
        if (r1 > r2 || c1 > c2) {
            throw new IllegalArgumentException("Invalid subgrid boundaries");
        }

        return prefix2D[r2 + 1][c2 + 1]
                - prefix2D[r1][c2 + 1]
                - prefix2D[r2 + 1][c1]
                + prefix2D[r1][c1];
    }

    public int length() {
        return length1D;
    }
}

