# SmartResponsor / Role — 0.9.0-rc1

## Включено

- Step 29: Policy Registry + Feature Flags + RegistryBackedPdp
- Step 30: Audit Trail + Export + AuditingPdp
- Step 31: Prometheus Metrics + OTel bridge + MetricsPdpV2
- Step 32: HMAC verify + anti-replay
- Step 33: Symfony Bundle (controllers + HMAC subscriber + DI)
- Step 34: PHP SDK (PSR-18) + TS SDK (fetch)
- Step 35: ACL коннекторы (PDO/JSON/GitHub/LDAP) + RoleResolver
- Step 36: Retention/Housekeeping (GC + архивация)

## Совместимость/интеграция

- API: `/v2/access/check` и `/v2/access/check:batch` (контроллеры Symfony + SDK'и).
- HMAC подпись: `v1=base64(hmac_sha256("POST {path}\n{Date}\n{Body}", secret))`.
- Метрики: Prometheus exposition text 0.0.4 на базе `PrometheusExporter`.
- Аудит: `role_audit` (SQLite/PostgreSQL), архив JSONL, TTL/чистки.

## Ломающие изменения

- Нет намеренно введённых breaking changes по сравнению с шагами 29–36.

## Известные ограничения

- В RC включены минимальные заглушки ядра (интерфейсы/значимые объекты) для самостоятельной сборки и lint.
- Кеширующий декоратор `CachedPdpV2` — упрощённый (no-op) в этом RC.
