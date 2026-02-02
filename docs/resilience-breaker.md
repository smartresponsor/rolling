# Resilience: Circuit Breaker + Exponential Backoff

- Interfaces: CircuitBreakerInterface, BackoffStrategyInterface, ResilientInvokerInterface
- Implementations: SimpleCircuitBreaker, ExponentialJitterBackoff, ResilientInvoker
- Time: SystemClock/SystemSleeper for portability
- Demo: `php tools/resilience/breaker_demo.php` → report/breaker_demo.json

## Notes

- Classifier treats HTTP-like 4xx codes as permanent, 5xx as transient.
- Breaker threshold/window/cooldown are configurable; demo uses 3 failures, 5s window, 0.5s cooldown.
