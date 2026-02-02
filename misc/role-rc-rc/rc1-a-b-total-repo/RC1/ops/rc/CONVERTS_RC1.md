# RC1 Envelopes (CONVERT)

## 1) Миграции БД (Audit + Replay)

```
ConvertMessage:
  task: "DB migrations for Role"
  code_limits:
    max_files: 3
  deliverables:
    - SQL for SQLite and PostgreSQL
  acceptance:
    - Tables exist: role_audit, replay_nonce
    - Indexes present
  run:
    bash:
      - "cat ops/db/sqlite/role_audit.sql"
      - "cat ops/db/sqlite/replay_nonce.sql"
```

## 2) Включение HMAC + Anti-replay (Symfony)

```
ConvertMessage:
  task: "Enable HMAC guard for /v2/access/check"
  code_limits: { max_files: 2 }
  acceptance:
    - Requests without valid signature → 401
    - Replay blocked within TTL
  files_touch:
    - config/packages/role.yaml
```

## 3) Экспорт метрик

```
ConvertMessage:
  task: "Expose /metrics"
  code_limits: { max_files: 2 }
  acceptance:
    - Prometheus scrape returns role_pdp_* metrics
```

## 4) Подключение ACL источников

```
ConvertMessage:
  task: "Wire ACL sources"
  inputs:
    - ops/acl/json.example.json
  acceptance:
    - Resolver.can() returns expected boolean for sample subjects
```
