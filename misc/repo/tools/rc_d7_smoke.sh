#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
bash "$ROOT/tools/sdk_ts_smoke.sh"
bash "$ROOT/tools/sdk_go_smoke.sh"
bash "$ROOT/tools/sdk_java_smoke.sh"
echo "RC-D7 smoke OK (aggregate)"
