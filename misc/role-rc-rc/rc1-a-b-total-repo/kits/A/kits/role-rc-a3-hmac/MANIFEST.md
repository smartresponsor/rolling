# RC-A3 — HMAC guard e2e (role- prefix)

## Что это

Набор для включения и проверки HMAC + anti-replay на эндпоинте `/v2/access/check` в Symfony-бандле.

## Что нужно уже иметь

- Подписчик `App\Integration\Symfony\EventSubscriber\HmacGuardSubscriber` из шага 32/33.
- Таблица `replay_nonce` (SQL прилагается).

## Что делает кит

- Включает HMAC‑guard через конфиг (`role.security`).
- Даёт скрипты для позитив/негатив проверки, включая replay.
