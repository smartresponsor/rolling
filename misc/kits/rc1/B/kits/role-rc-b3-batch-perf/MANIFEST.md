# RC-B3 — Batch perf (streaming + partial success)

## Что добавлено

- `CheckBatchProcessor` — потоковая обработка батча с ограничением памяти.
- Частичный успех: каждая запись получает `ok: true|false`; ошибки не роняют весь батч.
- Чанкинг: `chunkSize` (по умолчанию 128) и `maxItems` (по умолчанию 10000).
- Пример CLI `bin/role-batch-perf.php` для замера ms/item и p95.
- Потоковый API-враппер `ApiV2BatchStream` (пример интеграции с контроллером).

## Acceptance идеи

- 1000 запросов обрабатываются без OOM; p95 ≤ целевого X ms/item (подставьте X для своей среды).
