# RC-A4 — Audit + Janitor (архивирование/TTL)

## Что это

Кит для создания таблиц аудита `role_audit`, анти-реплея `replay_nonce`, включения джанитора (GC/архивирование) и
systemd‑таймера.

## Acceptance

- Запросы к `/v2/access/check` пишутся в `role_audit`.
- `role-janitor.timer` удаляет записи старше `retain_days` и создаёт JSONL(.gz) архив.
