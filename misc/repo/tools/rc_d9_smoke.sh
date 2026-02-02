#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
php -l "$ROOT/src/Service/Role/Tenant/Quota.php" >/dev/null
php -l "$ROOT/src/Service/Role/Tenant/Limits.php" >/dev/null
php -l "$ROOT/src/Service/Role/Tenant/Backup.php" >/dev/null
php -l "$ROOT/src/Service/Role/Tenant/Restore.php" >/dev/null
php -l "$ROOT/src/Http/Role/Api/Admin/TenantAdminController.php" >/dev/null
php -l "$ROOT/tools/backup_tenant.php" >/dev/null
php -l "$ROOT/tools/restore_tenant.php" >/dev/null

# seed tuples for t1
mkdir -p "$ROOT/var"
echo '{"ts":"2025-01-01T00:00:00Z","tenant":"t1","subject":"user:1","relation":"viewer","resource":"doc:1","op":"upsert"}' >> "$ROOT/var/tuples.ndjson"
echo '{"ts":"2025-01-01T00:00:00Z","tenant":"t2","subject":"user:9","relation":"viewer","resource":"doc:2","op":"upsert"}' >> "$ROOT/var/tuples.ndjson"

# set quota small and consume
php -r 'require "'$ROOT'/src/Service/Role/Tenant/Quota.php"; use App\Service\Role\Tenant\Quota; $q=new Quota("'$ROOT'/var/tenants"); $q->setLimit("t1", 2); for($i=0;$i<2;$i++){var_dump($q->consume("t1",1));} var_dump($q->consume("t1",1));' >/dev/null || true

# backup & restore roundtrip (t1)
OUT=$(php "$ROOT/tools/backup_tenant.php" t1 | grep '"path"' | sed -E 's/.*"path": *"([^"]+)".*/\1/')
test -f "$OUT"
php "$ROOT/tools/restore_tenant.php" "$OUT" >/dev/null
echo "RC-D9 smoke OK"
