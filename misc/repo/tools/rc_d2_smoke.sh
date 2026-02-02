#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
php -l "$ROOT/src/Service/Role/Model/SchemaRegistry.php" >/dev/null
php -l "$ROOT/src/Service/Role/Model/Diff.php" >/dev/null
php -l "$ROOT/src/Service/Role/Model/Migrator.php" >/dev/null
php -l "$ROOT/tools/model_diff.php" >/dev/null
php -l "$ROOT/tools/model_apply.php" >/dev/null
# Dry-run
php "$ROOT/tools/model_apply.php" v2_base "$ROOT/examples/schema/v2/v2_base.json" 1 | grep -q '"ok": true'
php "$ROOT/tools/model_apply.php" v2_add "$ROOT/examples/schema/v2/v2_add_fields.json" 1 | grep -q '"ok": true'
php "$ROOT/tools/model_apply.php" v2_add "$ROOT/examples/schema/v2/v2_add_fields.json" 0 | grep -q '"activated": "v2_add"'
php "$ROOT/tools/model_apply.php" v2_break "$ROOT/examples/schema/v2/v2_breaking_rename.json" 1 | grep -q '"breaking": true'
echo "RC-D2 smoke OK"
