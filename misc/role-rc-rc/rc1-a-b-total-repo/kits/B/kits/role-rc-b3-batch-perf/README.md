# RC-B3 — Batch perf (streaming + partial success)

- Потоковая обработка с чанками (по умолчанию 128).
- Ошибки помечаются на уровне элемента и не прерывают общий процесс.
- CLI `bin/role-batch-perf.php N SLEEP_US` — измеряет производительность локально.

Интеграция NDJSON:

```php
$api = new \Http\Role\V2\ApiV2BatchStream($processor);
$api->stream($input, function(string $line) {
    // echo $line; // писать в ответ с flush()
});
```
