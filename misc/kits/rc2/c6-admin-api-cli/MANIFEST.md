# RC-C6 — Admin API/CLI + metrics expansion (role-pre-rc2-c6-admin-api-cli)

- Admin REST for Policy Registry (import/activate/list/export).
- Admin REST for ReBAC stats/dump.
- CLI `bin/role-admin.php` mirrors REST operations.
- Token guard via `X-Admin-Token` == env `ROLE_ADMIN_TOKEN`.
- Metrics expansion: in-memory counters for admin ops, exportable to /metrics.
