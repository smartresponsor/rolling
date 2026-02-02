# Проверка RC-A2

## Предусловия

- Включён RoleBundle в `config/bundles.php`.
- Смерджены файлы из `config/` и контроллер `MetricsController`.

## Эндпоинты

### 1) Check

```bash
curl -s -X POST http://localhost:8000/v2/access/check \
  -H 'Content-Type: application/json' \
  -d '{"subjectId":"u1","action":"message.read","scopeType":"global"}' | jq .
```

Ожидаем: HTTP 200, JSON c `decision`.

### 2) Metrics

```bash
curl -s http://localhost:8000/metrics | head
```

Ожидаем: текст вида

```
# HELP role_pdp_requests_total ...
# TYPE role_pdp_requests_total counter
role_pdp_requests_total{component="remote"} 0
```
