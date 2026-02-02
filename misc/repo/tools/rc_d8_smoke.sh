#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
php -l "$ROOT/src/Service/Role/Explain/TupleReader.php" >/dev/null
php -l "$ROOT/src/Service/Role/Explain/Planner.php" >/dev/null
php -l "$ROOT/src/Service/Role/Explain/Renderer.php" >/dev/null
php -l "$ROOT/src/Http/Role/Api/ExplainController.php" >/dev/null
php -l "$ROOT/tools/explain_cli.php" >/dev/null
# seed a tuple and run CLI
mkdir -p "$ROOT/var"
echo '{"ts":"2025-01-01T00:00:00Z","tenant":"t1","subject":"user:1","relation":"viewer","resource":"doc:42","op":"upsert"}' >> "$ROOT/var/tuples.ndjson"
php "$ROOT/tools/explain_cli.php" user:1 viewer doc:42 t1 >/dev/null
echo "RC-D8 smoke OK"
