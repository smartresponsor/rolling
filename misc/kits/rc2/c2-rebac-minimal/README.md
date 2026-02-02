# RC-C2 — ReBAC minimal

- Tuple store (PDO + memory), Writer, Checker (recursive `object#relation` subjects).
- Endpoints: `/v2/rebac/tuple/write`, `/v2/rebac/check`.
- SQL: `ops/db/{sqlite,pgsql}/role_rebac.sql`.

Quick test:

```bash
php -l bin/role-rebac.php
php tools/rc_c2_smoke.sh
php bin/role-rebac.php write doc 1 viewer user 42
php bin/role-rebac.php check user:42 doc:1 viewer
```
