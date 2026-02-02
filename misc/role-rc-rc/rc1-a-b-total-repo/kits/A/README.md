# role-rc-a-total

Сводный пакет RC1-пути (A1..A5).

## Состав

- RC1/ — полный релиз-кандидат (код + SDK + ops).
- kits/role-rc-a1-smoke — Smoke+Lint+Tests.
- kits/role-rc-a2-symfony — Подключение Bundle, маршрутов, /metrics.
- kits/role-rc-a3-hmac — HMAC guard e2e + anti-replay.
- kits/role-rc-a4-janitor — Audit/Replay миграции + janitor/systemd.
- kits/role-rc-a5-sdk — Sanity PHP/TS SDK.

## Порядок

1) A1 smoke → 2) A2 wiring → 3) A3 HMAC → 4) A4 janitor → 5) A5 SDK sanity.
