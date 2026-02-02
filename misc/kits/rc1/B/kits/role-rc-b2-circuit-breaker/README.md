# RC-B2 — Circuit Breaker

Подключение:

```php
use App\Resilience\Role\CircuitBreakingPdpV2;use App\Resilience\Role\SystemClock;

/** @var \PolicyInterface\Role\PdpV2Interface $remote */
$pdp = new CircuitBreakingPdpV2($remote, breakerId: 'remote-pdp', failureThreshold: 3, openBaseSeconds: 5, openMaxSeconds: 60, clock: new SystemClock());
```

Поведение:

- пока < threshold — ошибки пробрасываются;
- при достижении threshold — breaker **open**, отдаём DENY с obligation `degraded`;
- после окна — **half-open**, 1 пробный вызов решает судьбу состояния.
