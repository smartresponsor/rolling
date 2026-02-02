# Policy Registry (RC-C3)

- Versioned registry for (ns, name) with unique versions.
- Single active version per policy name.
- Migrations are metadata records that also finalize with activation of `to` version.

## CLI

```bash
# SQLite init
sqlite3 ./var/policy.db < ops/db/sqlite/role_policy_registry.sql
export ROLE_POLICY_DSN="sqlite:./var/policy.db"
export ROLE_POLICY_NS="acme"

# Import two versions
php bin/role-policy.php import doc-view 1.0.0 examples/policies/doc-view_1.0.0.json
php bin/role-policy.php import doc-view 1.1.0 examples/policies/doc-view_1.1.0.json

# Activate & list
php bin/role-policy.php activate doc-view 1.0.0
php bin/role-policy.php list doc-view

# Migrate (records metadata) and activate v1.1.0
php bin/role-policy.php migrate doc-view 1.0.0 1.1.0 "add deny banned"
```

## Integration

Service `RegistryService` wraps store and exposes import/export/activate.
