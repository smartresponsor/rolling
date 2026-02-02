# Проверка RC-B3 (batch perf)

## 1) Быстрый перф замер (без сети)

```bash
php bin/role-batch-perf.php 2000 0
# ответ: {"n":2000,"sleep_us":0,"duration_ms":..., "per_item_ms":..., "peak_mb":...}
```

## 2) Эмуляция задержки (например, 200 мкс)

```bash
php bin/role-batch-perf.php 1000 200
# следите за per_item_ms и peak_mb
```

## 3) Врезка в контроллер (NDJSON)

Пример в `src/Http/Role/V2/ApiV2BatchStream.php`: передайте `$emit`, который пишет в поток ответа.
