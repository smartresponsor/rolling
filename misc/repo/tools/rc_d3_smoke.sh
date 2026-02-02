#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
php -l "$ROOT/src/Http/Role/Api/Consistency.php" >/dev/null
php -l "$ROOT/src/Http/Role/Api/WatchController.php" >/dev/null
php -l "$ROOT/src/Service/Role/Cache/Cache.php" >/dev/null
php -l "$ROOT/src/Service/Role/Cache/Partitioner.php" >/dev/null
php -l "$ROOT/src/Service/Role/Cache/Invalidation.php" >/dev/null
php -l "$ROOT/tools/watch_tail.php" >/dev/null
php -l "$ROOT/tools/watch_emit.php" >/dev/null
# quick simulate
php "$ROOT/tools/watch_emit.php" >/dev/null
timeout 1 php "$ROOT/tools/watch_tail.php" >/dev/null || true
echo "RC-D3 smoke OK"
