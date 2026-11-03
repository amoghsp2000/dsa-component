package com.dsatoolbox.examples;

import com.dsatoolbox.*;
import java.util.ArrayList;
import java.util.List;

/**
 * Enterprise Recharge and Billing Demo in Java
 * Combines BinarySearch (tier brackets), PriorityQueue (urgent scheduling), and BloomFilter (idempotency).
 */
public class RechargeBillingDemo {

    public static void main(String[] args) {
        System.out.println("=== Running Java Recharge and Billing Pipeline Demo ===\n");

        // 1. Fee tiers using BinarySearch
        List<BinarySearch.Tier<String>> tiers = new ArrayList<>();
        tiers.add(new BinarySearch.Tier<>(10.0, "Bronze Top-Up (1% Cashback)"));
        tiers.add(new BinarySearch.Tier<>(50.0, "Silver Top-Up (2.5% Cashback)"));
        tiers.add(new BinarySearch.Tier<>(100.0, "Gold Top-Up (4% Cashback)"));
        tiers.add(new BinarySearch.Tier<>(500.0, "Platinum Bill-Pay (7% Cashback)"));

        double amount = 75.0;
        String tier = BinarySearch.findTier(tiers, amount);
        System.out.println("Recharge of $" + amount + " resolved to tier: " + tier);

        // 2. Idempotency pre-check using BloomFilter
        BloomFilter filter = new BloomFilter(10000, 0.01);
        String idempKey = "TXN_RECHARGE_9901";
        System.out.println("Before insert: contains(" + idempKey + ") = " + filter.contains(idempKey));
        filter.add(idempKey);
        System.out.println("After insert: contains(" + idempKey + ") = " + filter.contains(idempKey));

        // 3. Priority scheduling using PriorityQueue
        PriorityQueue<String> queue = new PriorityQueue<>(false); // Max-heap: higher score first
        queue.push("Order #1 (Standard Prepaid)", 100.0);
        queue.push("Order #2 (Hospital Emergency Utility)", 1500.0);
        queue.push("Order #3 (Postpaid Bill)", 500.0);

        System.out.println("\nDispatching tasks by priority:");
        while (!queue.isEmpty()) {
            System.out.println("  -> Dispatched: " + queue.pop());
        }

        System.out.println("\nJava demo completed successfully!");
    }
}

