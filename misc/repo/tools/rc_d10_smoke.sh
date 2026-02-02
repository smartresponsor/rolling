#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
php -l "$ROOT/src/Http/Role/Api/CheckController.php" >/dev/null
php -l "$ROOT/config/routes/role_check.yaml" >/dev/null
php -l "$ROOT/tools/check_cli.php" >/dev/null
# seed tuples and run CLI
mkdir -p "$ROOT/var/log/role" "$ROOT/var"
echo '{"ts":"2025-01-01T00:00:00Z","tenant":"t1","subject":"user:1","relation":"viewer","resource":"doc:42","op":"upsert"}' >> "$ROOT/var/tuples.ndjson"
php "$ROOT/tools/check_cli.php" user:1 viewer doc:42 t1 | grep -q '"allowed":true'
echo "RC-D10 smoke OK"
