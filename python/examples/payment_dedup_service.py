"""
Payment Deduplication and Fraud Detection Service in Python
Demonstrates real-world integration of BloomFilter and UnionFind.
"""

import sys
from pathlib import Path

# Add src to sys.path
sys.path.insert(0, str(Path(__file__).resolve().parent.parent / "src"))

from bloom_filter import BloomFilter
from union_find import UnionFind


class PaymentService:
    def __init__(self) -> None:
        self.bloom = BloomFilter(expected_elements=50000, false_positive_rate=0.001)
        self.fraud_detector = UnionFind()
        self.committed_db = set()

    def process_payment(self, idempotency_key: str, sender: str, receiver: str, amount: float) -> dict:
        # Step 1: Bloom filter check
        if self.bloom.contains(idempotency_key):
            if idempotency_key in self.committed_db:
                return {"status": "REJECTED_DUPLICATE", "key": idempotency_key}

        # Step 2: Fraud ring cycle detection
        is_cycle = not self.fraud_detector.union(sender, receiver)
        cluster_size = self.fraud_detector.get_component_size(sender)

        if is_cycle and cluster_size >= 3:
            return {
                "status": "FLAGGED_FRAUD_RING",
                "reason": f"Circular payment flow detected between {sender} and {receiver}",
                "cluster_size": cluster_size
            }

        # Step 3: Commit
        self.bloom.add(idempotency_key)
        self.committed_db.add(idempotency_key)

        return {"status": "SUCCESS", "amount": amount, "sender": sender, "receiver": receiver}


if __name__ == "__main__":
    svc = PaymentService()
    print("=== Payment Service Python Demo ===")
    r1 = svc.process_payment("idemp_101", "Alice", "Bob", 100.0)
    r2 = svc.process_payment("idemp_102", "Bob", "Charlie", 80.0)
    print("P1:", r1)
    print("P2:", r2)

    # Duplicate check
    dup = svc.process_payment("idemp_101", "Alice", "Bob", 100.0)
    print("Duplicate:", dup)

    # Fraud ring check (Charlie -> Alice closes Alice -> Bob -> Charlie -> Alice)
    fraud = svc.process_payment("idemp_103", "Charlie", "Alice", 60.0)
    print("Fraud Check:", fraud)

