# Multi-tenant operations (RC-D9)

Capabilities:

- **Quota** per tenant (per-minute window). Headers to expose on 429:
    - `X-RateLimit-Limit`, `X-RateLimit-Remaining`, `Retry-After` (seconds to reset).
- **Limits** (static): `max_tuples`, `residency` (e.g., `eu`, `us`). Use to route storage
  under `var/residency/<region>/...`.
- **Backup/Restore**: zip with `tenant.json`, `tuples.ndjson`
  slice, `tenants/{quota_limits.json, quota_usage.json, limits.json}`.

HTTP (admin, requires D4 admin voter headers):

- `GET /v2/admin/tenants/quota?tenant=t1`
- `POST /v2/admin/tenants/quota` body: `{ "tenant":"t1", "per_min": 1200 }`
- `POST /v2/admin/tenants/backup` body: `{ "tenant":"t1" }` → returns `{ok,path}`
- `POST /v2/admin/tenants/restore` body: `{ "path":"/abs/path/to/zip" }` (OWNER-only)

PHP usage (server-side enforcement example):

```php
use App\Legacy\Service\Tenant\Quota;
$q = new Quota(__DIR__.'/var/tenants');
$r = $q->consume('t1', 1);
if (!$r['allowed']) { /* return 429 with X-RateLimit-* headers */ }
```
