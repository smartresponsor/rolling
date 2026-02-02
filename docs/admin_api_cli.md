# Admin API/CLI (RC-C6)

## Security

- header `X-Admin-Token` must equal env `ROLE_ADMIN_TOKEN`.
- On failure: `{"ok":false,"error":"admin_unauthorized"}` 401.

## REST

```
POST /v2/admin/policy/import   {ns,name,version,doc:string(json)}
POST /v2/admin/policy/activate {ns,name,version}
GET  /v2/admin/policy/list?ns=&name=
GET  /v2/admin/policy/export?ns=&name=&version=
GET  /v2/admin/rebac/stats?ns=
```

## CLI

```bash
# SQLite registries
sqlite3 ./var/policy.db < ops/db/sqlite/role_policy_registry.sql
sqlite3 ./var/rebac.db  < ops/db/sqlite/role_rebac.sql
export ROLE_POLICY_DSN="sqlite:./var/policy.db"
export ROLE_REBAC_DSN="sqlite:./var/rebac.db"
export ROLE_ADMIN_NS="acme"
export ROLE_ADMIN_TOKEN="devtoken"

# Import & activate
php bin/role-admin.php policy:import doc-view 1.0.0 examples/policies/doc-view_1.0.0.json
php bin/role-admin.php policy:activate doc-view 1.0.0
php bin/role-admin.php policy:list doc-view

# ReBAC stats
php bin/role-admin.php rebac:stats
```

## Metrics (Prometheus)

- in-memory counters (see `App\Metrics\Role\Admin\AdminMetrics`).
- Wire into your `/metrics` exporter: append `AdminMetrics::renderPrometheus()`.
