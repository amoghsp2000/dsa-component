"""
Streaming Telemetry Metrics with Monotonic Queue & Prefix Sum in Python
"""

import sys
from pathlib import Path

sys.path.insert(0, str(Path(__file__).resolve().parent.parent / "src"))

from sliding_window import SlidingWindow
from prefix_sum import PrefixSum


def analyze_traffic_stream():
    # Simulated 1-second interval latency metrics (in milliseconds)
    latencies = [120, 145, 98, 350, 420, 110, 95, 88, 510, 130, 115]
    window_size = 3

    print(f"Latency stream: {latencies}")
    print(f"Sliding window size: {window_size}")

    # Compute rolling maximums
    peak_latencies = SlidingWindow.compute_sliding_max(latencies, window_size)
    print(f"Sliding window peak latencies: {peak_latencies}")

    # Compute interval averages using PrefixSum
    ps = PrefixSum(latencies)
    # Range [3, 5] correspond to values [350, 420, 110]
    total = ps.query_range(3, 5)
    avg = total / 3
    print(f"Total latency sum for burst interval [3, 5]: {total}ms (Average: {avg:.1f}ms)")


if __name__ == "__main__":
    print("=== Streaming Metrics Demo ===")
    analyze_traffic_stream()

