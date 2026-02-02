# role-rc2-total

Integrated working tree for **Role RC2** (C2…C7 merged into `repo/`).

- `kits/` — original kits unpacked
- `repo/` — integrated code (src/, tests/, bin/, tools/, ops/, config/, sdk/)

## Quick start (SQLite)

```bash
cd repo

# DB init
mkdir -p var
sqlite3 ./var/rebac.db  < ops/db/sqlite/role_rebac.sql
sqlite3 ./var/policy.db < ops/db/sqlite/role_policy_registry.sql
export ROLE_REBAC_DSN="sqlite:./var/rebac.db"
export ROLE_POLICY_DSN="sqlite:./var/policy.db"
export ROLE_POLICY_NS="acme"
export ROLE_ADMIN_NS="acme"
export ROLE_ADMIN_TOKEN="devtoken"

# OPA (optional for C4)
opa run --server -a :8181 examples/rego/role_v2.rego &

# Smokes
./tools/rc2_smoke.sh

# Minimal data
php bin/role-policy.php import doc-view 1.0.0 ../kits/c3-policy-registry/examples/policies/doc-view_1.0.0.json
php bin/role-policy.php activate doc-view 1.0.0
php bin/role-rebac.php write doc 1 viewer user 42
php bin/role-rebac.php check user:42 doc:1 viewer
```

## HTTP endpoints (Symfony routes)

- ReBAC: `POST /v2/rebac/tuple/write`, `POST /v2/rebac/check`
- Admin: `POST /v2/admin/policy/import|activate`, `GET /v2/admin/policy/list|export`, `GET /v2/admin/rebac/stats`
- Consistency headers helper: `X-Role-Consistency`, `ETag`

## Notes

- Naming: role-pre-rc2-* kits, integrated here for RC2.
- Code style: single-hyphen paths, EN comments, layer-first isolation.
