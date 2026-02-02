# RC-B4 — Bench & Profile (микробенчи + отчёт)

## Что входит

- `bin/role-bench.php` — автономный бенчмарк без внешних зависимостей.
- `tools/run_bench.sh|ps1` — запуск и сбор отчётов.
- `report/bench/*.json|*.csv` — сырые результаты.
- `docs/benchmark.md` — итоговый отчёт (генерится скриптом).
- (опц.) `phpbench.json` + `bench/PhpBench/*.php` — если есть `phpbench` в проекте.

## Наборы

- **serial_ctx** — нормализация контекста + `json_encode` (аналог хэша контекста).
- **cache_hit** — горячий кеш (модель PDP-декоратора).
- **rpc_sim** — имитация сетевой задержки (usleep).
- **batch_proc** — потоковая обработка батча (если класс доступен).

## Acceptance

- Есть `docs/benchmark.md` с таблицей p50/p95/throughput per scenario.
- Приложены сырые JSON/CSV результаты.
