# role-rc-b-total

Сводный пакет RC2-пути (B1..B5).

## Состав

- kits/role-rc-b1-cached-pdp-v2 — кеш PDP v2 + инвалидации
- kits/role-rc-b2-circuit-breaker — Circuit Breaker
- kits/role-rc-b3-batch-perf — потоковый батч + partial success
- kits/role-rc-b4-bench-profile — микробенчи и отчёт
- kits/role-rc-b5-sdk-polish — TS ESM+d.ts, PHP исключения

## Порядок

1) B1 кеш → 2) B2 breaker → 3) B3 batch → 4) B4 bench → 5) B5 SDK.
