# Проверка RC-A4 (Audit + Janitor)

## 0) Миграции

SQLite:

```bash
sqlite3 /var/lib/smartresponsor/role.sqlite < ops/db/sqlite/role_audit.sql
sqlite3 /var/lib/smartresponsor/role.sqlite < ops/db/sqlite/replay_nonce.sql
```

PostgreSQL:

```bash
psql "$PG_DSN" -f ops/db/pgsql/role_audit.sql
psql "$PG_DSN" -f ops/db/pgsql/replay_nonce.sql
```

## 1) Проверка аудита

Вызовите `/v2/access/check`, затем убедитесь:

```sql
SELECT COUNT(*) FROM role_audit;
```

## 2) Ручной запуск GC

```bash
ROLE_AUDIT_DSN="sqlite:/var/lib/smartresponsor/role.sqlite" php bin/role-janitor.php gc ops/retention.json
```

## 3) systemd

```bash
sudo cp ops/systemd/role-janitor.* /etc/systemd/system/
sudo systemctl daemon-reload
sudo systemctl enable --now role-janitor.timer
sudo systemctl status role-janitor.timer
```
