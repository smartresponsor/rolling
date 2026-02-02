# RC-A5 — SDK sanity (PHP PSR-18 + Node/TS)

## Что проверяем

- PHP SDK (PSR-18/17/7) — `check()` на живом эндпоинте `/v2/access/check`.
- TS SDK (Node 18+) — `check()` через `fetch`/WebCrypto (в SDK реализовано).

## Переменные окружения

- `ROLE_PDP_BASE_URL` (пример: http://localhost:8000)
- `ROLE_PDP_API_KEY` (опц.)
- `ROLE_PDP_HMAC` (опц. для подписи)

## Acceptance

- Оба примера возвращают HTTP 200 с JSON, поле `decision` присутствует.
- `report/sdk_sanity.txt` содержит сводку PASS/FAIL.
