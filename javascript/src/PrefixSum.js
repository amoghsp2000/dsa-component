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
class PrefixSum {
  constructor(values = []) {
    this.prefix1D = [];
    this.length1D = 0;

    this.prefix2D = [];
    this.rows = 0;
    this.cols = 0;

    if (values.length > 0) {
      this.build1D(values);
    }
  }

  build1D(values) {
    this.length1D = values.length;
    this.prefix1D = new Array(this.length1D + 1).fill(0);

    for (let i = 0; i < this.length1D; i++) {
      this.prefix1D[i + 1] = this.prefix1D[i] + values[i];
    }
  }

  queryRange(left, right) {
    if (left < 0 || right >= this.length1D) {
      throw new RangeError(`Indices [${left}, ${right}] out of bounds [0, ${this.length1D - 1}]`);
    }
    if (left > right) {
      throw new Error(`Left index (${left}) cannot be greater than right index (${right})`);
    }

    return this.prefix1D[right + 1] - this.prefix1D[left];
  }

  build2D(matrix) {
    this.rows = matrix.length;
    this.cols = this.rows > 0 ? matrix[0].length : 0;

    if (this.rows === 0 || this.cols === 0) {
      this.prefix2D = [];
      return;
    }

    this.prefix2D = Array.from({ length: this.rows + 1 }, () => new Array(this.cols + 1).fill(0));

    for (let r = 0; r < this.rows; r++) {
      for (let c = 0; c < this.cols; c++) {
        this.prefix2D[r + 1][c + 1] =
          matrix[r][c] +
          this.prefix2D[r][c + 1] +
          this.prefix2D[r + 1][c] -
          this.prefix2D[r][c];
      }
    }
  }

  query2D(r1, c1, r2, c2) {
    if (r1 < 0 || r2 >= this.rows || c1 < 0 || c2 >= this.cols) {
      throw new RangeError('Subgrid indices out of bounds');
    }
    if (r1 > r2 || c1 > c2) {
      throw new Error('Invalid subgrid boundaries');
    }

    return (
      this.prefix2D[r2 + 1][c2 + 1] -
      this.prefix2D[r1][c2 + 1] -
      this.prefix2D[r2 + 1][c1] +
      this.prefix2D[r1][c1]
    );
  }

  get length() {
    return this.length1D;
  }
}

if (typeof module !== 'undefined' && module.exports) {
  module.exports = { PrefixSum };
}

