# Проверка RC-A3 (HMAC + anti-replay)

## 0) Подготовка

- Применить SQL для `replay_nonce` (см. ops/db/*/replay_nonce.sql).
- В `.env.local` указать `ROLE_PDP_HMAC=...`, `ROLE_AUDIT_DSN=...`.
- Добавить/слить `config/packages/role.security.yaml` (включает guard).

## 1) Без подписи → 401

```bash
curl -i -s -X POST http://localhost:8000/v2/access/check \
  -H 'Content-Type: application/json' \
  -d '{"subjectId":"u1","action":"message.read","scopeType":"global"}' | sed -n '1,20p'
# Ожидаем: 401, заголовок X-Auth-Error: missing_signature
```

## 2) Корректная подпись → 200

```bash
bash tools/hmac_send.sh http://localhost:8000 POST /v2/access/check \
  '{"subjectId":"u1","action":"message.read","scopeType":"global"}' "$ROLE_PDP_HMAC" | jq .
# Ожидаем: HTTP 200 JSON
```

## 3) Replay → 401

```bash
bash tools/hmac_replay_test.sh http://localhost:8000 POST /v2/access/check \
  '{"subjectId":"u1","action":"message.read","scopeType":"global"}' "$ROLE_PDP_HMAC"
# Ожидаем: второй ответ 401, заголовок X-Auth-Error: replay
```

## 4) Сдвиг даты → 401

Сымитируйте устаревшую дату, подставив фиксированный `Date` в tools/hmac_sign.php и руками отправив запрос.
