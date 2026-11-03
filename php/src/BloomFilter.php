<?php

declare(strict_types=1);

namespace DsaToolbox;

use InvalidArgumentException;

/**
 * Bloom Filter
 *
 * Traditional DSA Category: Advanced Data Structures (Bit Manipulation + Hashing)
 * Real-World Engineering Use Cases:
 *  - Data Deduplication and Validation (idempotency key pre-filtering before DB queries)
 *  - Database Optimization (eliminating disk reads for non-existent records / anti-cache penetration)
 *  - Financial and Payment Systems (checking transaction references, duplicate order prevention)
 *
 * Guarantees:
 *  - False Negatives: NEVER (if contains() returns false, item is 100% not in set)
 *  - False Positives: Tunable probability (e.g. 1%, 0.1%)
 *
 * Time Complexity:
 *  - add: O(K) where K is number of hash iterations
 *  - contains: O(K)
 * Space Complexity: O(M) bits
 */
class BloomFilter
{
    private int $bitSize;
    private int $hashCount;
    private string $bitArray;
    private int $itemCount = 0;

    /**
     * @param int $expectedElements Expected number of items to store (N)
     * @param float $falsePositiveRate Acceptable error probability between 0 and 1 (e.g. 0.01 for 1%)
     * @throws InvalidArgumentException
     */
    public function __construct(int $expectedElements, float $falsePositiveRate = 0.01)
    {
        if ($expectedElements < 1) {
            throw new InvalidArgumentException("expectedElements must be at least 1");
        }
        if ($falsePositiveRate <= 0.0 || $falsePositiveRate >= 1.0) {
            throw new InvalidArgumentException("falsePositiveRate must be strictly between 0 and 1");
        }

        // Optimal bit size: m = -(n * ln(p)) / (ln(2)^2)
        $this->bitSize = (int) ceil(-($expectedElements * log($falsePositiveRate)) / (log(2) ** 2));
        // Round up to nearest byte boundary
        $this->bitSize = max(64, (int) (ceil($this->bitSize / 8) * 8));

        // Optimal hash count: k = (m / n) * ln(2)
        $this->hashCount = max(1, (int) round(($this->bitSize / $expectedElements) * log(2)));

        // Allocate byte string buffer
        $byteLength = (int) ($this->bitSize / 8);
        $this->bitArray = str_repeat("\0", $byteLength);
    }

    /**
     * Adds an item to the Bloom Filter.
     */
    public function add(string $item): void
    {
        [$h1, $h2] = $this->hashPair($item);

        for ($i = 0; $i < $this->hashCount; $i++) {
            // Kirsch-Mitzenmacher optimization: gi(x) = h1(x) + i * h2(x)
            $bitIndex = (int) fmod(abs($h1 + $i * $h2), $this->bitSize);
            $this->setBit($bitIndex);
        }

        $this->itemCount++;
    }

    /**
     * Checks if an item is possibly in the set.
     * Returns false if item is DEFINITIVELY NOT in the set.
     * Returns true if item is PROBABLY in the set.
     */
    public function contains(string $item): bool
    {
        [$h1, $h2] = $this->hashPair($item);

        for ($i = 0; $i < $this->hashCount; $i++) {
            $bitIndex = (int) fmod(abs($h1 + $i * $h2), $this->bitSize);
            if (!$this->testBit($bitIndex)) {
                return false;
            }
        }

        return true;
    }

    public function getBitSize(): int
    {
        return $this->bitSize;
    }

    public function getHashCount(): int
    {
        return $this->hashCount;
    }

    public function getItemCount(): int
    {
        return $this->itemCount;
    }

    /**
     * Returns theoretical false positive rate given current item count.
     */
    public function getEstimatedFalsePositiveRate(): float
    {
        if ($this->itemCount === 0) {
            return 0.0;
        }
        // (1 - e^(-k * n / m))^k
        return pow(1 - exp(-($this->hashCount * $this->itemCount) / $this->bitSize), $this->hashCount);
    }

    public function clear(): void
    {
        $byteLength = (int) ($this->bitSize / 8);
        $this->bitArray = str_repeat("\0", $byteLength);
        $this->itemCount = 0;
    }

    private function setBit(int $index): void
    {
        $bytePos = intdiv($index, 8);
        $bitPos = $index % 8;
        $this->bitArray[$bytePos] = chr(ord($this->bitArray[$bytePos]) | (1 << $bitPos));
    }

    private function testBit(int $index): bool
    {
        $bytePos = intdiv($index, 8);
        $bitPos = $index % 8;
        return (ord($this->bitArray[$bytePos]) & (1 << $bitPos)) !== 0;
    }

    /**
     * Computes two independent 64-bit integer hashes from an input string.
     *
     * @return array{int, int}
     */
    private function hashPair(string $item): array
    {
        $md5 = md5($item);
        $h1 = hexdec(substr($md5, 0, 8));
        $h2 = hexdec(substr($md5, 8, 8));
        return [$h1, $h2];
    }
}

