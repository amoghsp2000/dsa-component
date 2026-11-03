/**
 * Express / Node.js Rate Limiting Middleware Example
 * Uses SlidingWindowRateLimiter to enforce sliding window quotas with sub-second accuracy.
 */

const { SlidingWindowRateLimiter } = require('../src/SlidingWindowRateLimiter');

/**
 * Creates an Express-compatible rate limiting middleware.
 * @param {number} maxRequests
 * @param {number} windowSeconds
 */
function createRateLimiterMiddleware(maxRequests = 5, windowSeconds = 10) {
  const limiter = new SlidingWindowRateLimiter(maxRequests, windowSeconds);

  return function rateLimiterMiddleware(req, res, next) {
    const clientKey = req.ip || req.headers['x-forwarded-for'] || 'anonymous';
    const now = Date.now() / 1000;

    const allowed = limiter.allow(clientKey, now);
    const remaining = limiter.getRemaining(clientKey, now);

    res.setHeader('X-RateLimit-Limit', maxRequests);
    res.setHeader('X-RateLimit-Remaining', remaining);

    if (!allowed) {
      const resetTime = limiter.getResetTime(clientKey, now);
      const retryAfter = Math.max(1, Math.ceil(resetTime - now));
      res.setHeader('Retry-After', retryAfter);
      return res.status(429).json({
        error: 'Too Many Requests',
        message: `Quota of ${maxRequests} requests per ${windowSeconds}s exceeded.`,
        retryAfterSeconds: retryAfter
      });
    }

    next();
  };
}

// Demo simulation without needing express installed
function runDemo() {
  console.log('=== Running Express Rate Limiter Middleware Demo ===\n');

  const middleware = createRateLimiterMiddleware(3, 5);

  const mockReq = { ip: '203.0.113.195' };
  const mockNext = () => console.log('  -> [NEXT] Request forwarded to route handler.');

  for (let i = 1; i <= 5; i++) {
    const headers = {};
    const mockRes = {
      setHeader: (k, v) => { headers[k] = v; },
      status: (code) => ({
        json: (data) => console.log(`  -> [HTTP ${code}] Response:`, data, 'Headers:', headers)
      })
    };

    console.log(`Incoming request #${i}:`);
    middleware(mockReq, mockRes, mockNext);
  }
}

if (require.main === module) {
  runDemo();
}

module.exports = { createRateLimiterMiddleware };

