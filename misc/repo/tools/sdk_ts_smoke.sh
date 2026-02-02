#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
test -f "$ROOT/sdk/ts/role/src/client.ts"
node -v >/dev/null 2>&1 && echo "node ok" || echo "node missing (skip)"
echo "RC-D7 TS smoke OK (static)"
