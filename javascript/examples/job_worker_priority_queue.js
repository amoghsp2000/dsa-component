/**
 * Background Job Worker Priority Queue Example in Node.js
 * Combines PriorityQueue and RetryQueue to schedule and execute high-priority billing tasks.
 */

const { PriorityQueue } = require('../src/PriorityQueue');
const { RetryQueue } = require('../src/RetryQueue');

class BackgroundJobWorker {
  constructor() {
    this.priorityQueue = new PriorityQueue(false); // Max-heap: higher priority score runs first
    this.retryQueue = new RetryQueue(0.5, 5.0);
  }

  submitJob(jobId, type, payload, priorityScore) {
    this.priorityQueue.push({ jobId, type, payload }, priorityScore);
    console.log(`[SUBMIT] Job ${jobId} (${type}) enqueued with priority score ${priorityScore}`);
  }

  processBatch(executor) {
    console.log('\n--- Processing Job Batch ---');

    // 1. Process due retries
    const retries = this.retryQueue.pollReady();
    for (const r of retries) {
      console.log(`[RETRY-EXEC] Retrying job ${r.id} (Attempt ${r.attempts})`);
      const ok = executor(r.payload);
      if (!ok) {
        this.retryQueue.retry(r.id, 'Worker timeout');
      }
    }

    // 2. Process priority queue jobs
    while (!this.priorityQueue.isEmpty()) {
      const job = this.priorityQueue.pop();
      console.log(`[EXEC] Running job ${job.jobId} (${job.type})`);
      const ok = executor(job);
      if (!ok) {
        console.log(`  -> Job ${job.jobId} failed. Enqueuing to retry queue with jitter.`);
        this.retryQueue.enqueue(job.jobId, job, 3, 0.1);
      } else {
        console.log(`  -> Job ${job.jobId} succeeded.`);
      }
    }
  }
}

if (require.main === module) {
  console.log('=== Running Background Job Worker Priority Queue Demo ===\n');

  const worker = new BackgroundJobWorker();
  worker.submitJob('JOB-01', 'BULK_EMAIL', { to: 'marketing' }, 10);
  worker.submitJob('JOB-02', 'EMERGENCY_RECHARGE', { phone: '+15551234' }, 100);
  worker.submitJob('JOB-03', 'PAYMENT_SETTLEMENT', { tx: 'tx_889' }, 80);

  worker.processBatch((job) => {
    // Simulate failure for payment settlement on first try
    if (job.jobId === 'JOB-03') return false;
    return true;
  });
}

module.exports = { BackgroundJobWorker };

