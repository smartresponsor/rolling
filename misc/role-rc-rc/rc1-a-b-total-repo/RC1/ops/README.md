# Role / Step 36 — Retention / Housekeeping

## Конфиг

`ops/retention.json`:

```json
{
  "audit": {
    "retain_days": 30,
    "archive_before_delete": true,
    "archive_path": "/var/log/smartresponsor/role_audit_%Y-%m.jsonl",
    "gzip": true,
    "batch": 1000
  },
  "replay": { "batch": 5000 }
}
```

> `archive_path` допускает `%Y-%m` — заменится текущей датой. Если путь укажете без плейсхолдеров, лог будет апендиться.

## CLI

```bash
# GC по конфигу (исп. ROLE_AUDIT_DSN = sqlite:/var/lib/role.sqlite или pgsql:...)
php bin/role-janitor.php gc ops/retention.json

# Только аудит: удалить старше N дней
php bin/role-janitor.php gc-audit 45 2000

# Только анти-реплей
php bin/role-janitor.php gc-replay 10000

# Архивировать + удалить (старше N дней) в JSONL (с gzip=true добавится .gz)
php bin/role-janitor.php archive-audit 90 /var/log/smartresponsor/role_audit_older90.jsonl 2000 1
```

## systemd‑таймер

Скопируйте `ops/systemd/*.service|*.timer`, затем:

```bash
sudo systemctl enable --now role-janitor.timer
sudo systemctl list-timers | grep role-janitor
```

## Примечания

- Для **PostgreSQL** колонка `ts` — `TIMESTAMPTZ`; мы используем `to_timestamp(:ts)`.
- Удаление делается батчами: SELECT id ... LIMIT —> DELETE WHERE id IN (...).
- При архиве пишем **JSONL** (по строке на запись) и только после этого удаляем эти id.
- Если размер аудита большой, рассмотрите партиционирование по месяцам (вне этого шага).
