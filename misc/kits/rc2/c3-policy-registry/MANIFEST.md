# RC-C3 — Policy Registry (versioned) — role-pre-rc2-c3-policy-registry

- Versioned registry for policies (ns, name, version, doc JSON).
- Activate one version per (ns,name). Tracks migrations from->to.
- Stores: PDO (sqlite/pgsql) and in-memory.
- CLI: import/export/list/activate/migrate.
- Consistency token rev bumps on changes.
