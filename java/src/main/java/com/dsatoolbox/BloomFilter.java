package com.dsatoolbox;

import java.nio.charset.StandardCharsets;
import java.security.MessageDigest;
import java.security.NoSuchAlgorithmException;
import java.util.BitSet;

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
public class BloomFilter {

    private final int bitSize;
    private final int hashCount;
    private final BitSet bitSet;
    private int itemCount = 0;

    public BloomFilter(int expectedElements, double falsePositiveRate) {
        if (expectedElements < 1) {
            throw new IllegalArgumentException("expectedElements must be at least 1");
        }
        if (falsePositiveRate <= 0.0 || falsePositiveRate >= 1.0) {
            throw new IllegalArgumentException("falsePositiveRate must be strictly between 0 and 1");
        }

        // Optimal bit size: m = -(n * ln(p)) / (ln(2)^2)
        int rawM = (int) Math.ceil(-(expectedElements * Math.log(falsePositiveRate)) / (Math.log(2) * Math.log(2)));
        this.bitSize = Math.max(64, (int) (Math.ceil(rawM / 8.0) * 8));

        // Optimal hash count: k = (m / n) * ln(2)
        this.hashCount = Math.max(1, (int) Math.round(((double) bitSize / expectedElements) * Math.log(2)));
        this.bitSet = new BitSet(bitSize);
    }

    public synchronized void add(String item) {
        long[] hashes = hashPair(item);
        long h1 = hashes[0];
        long h2 = hashes[1];

        for (int i = 0; i < hashCount; i++) {
            int bitIndex = (int) Math.floorMod(h1 + (long) i * h2, (long) bitSize);
            bitSet.set(bitIndex);
        }

        itemCount++;
    }

    public synchronized boolean contains(String item) {
        long[] hashes = hashPair(item);
        long h1 = hashes[0];
        long h2 = hashes[1];

        for (int i = 0; i < hashCount; i++) {
            int bitIndex = (int) Math.floorMod(h1 + (long) i * h2, (long) bitSize);
            if (!bitSet.get(bitIndex)) {
                return false;
            }
        }

        return true;
    }

    public int getBitSize() {
        return bitSize;
    }

    public int getHashCount() {
        return hashCount;
    }

    public synchronized int getItemCount() {
        return itemCount;
    }

    public synchronized double getEstimatedFalsePositiveRate() {
        if (itemCount == 0) return 0.0;
        return Math.pow(1.0 - Math.exp(-((double) hashCount * itemCount) / bitSize), hashCount);
    }

    public synchronized void clear() {
        bitSet.clear();
        itemCount = 0;
    }

    private long[] hashPair(String item) {
        try {
            MessageDigest md = MessageDigest.getInstance("MD5");
            byte[] digest = md.digest(item.getBytes(StandardCharsets.UTF_8));

            long h1 = ((long) (digest[0] & 0xFF) << 24)
                    | ((long) (digest[1] & 0xFF) << 16)
                    | ((long) (digest[2] & 0xFF) << 8)
                    | ((long) (digest[3] & 0xFF));

            long h2 = ((long) (digest[4] & 0xFF) << 24)
                    | ((long) (digest[5] & 0xFF) << 16)
                    | ((long) (digest[6] & 0xFF) << 8)
                    | ((long) (digest[7] & 0xFF));

            return new long[]{h1, h2};
        } catch (NoSuchAlgorithmException e) {
            throw new RuntimeException("MD5 digest algorithm not found", e);
        }
    }
}

