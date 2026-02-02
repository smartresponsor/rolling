#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
run_if() { local f="$1"; if [ -x "$f" ]; then echo ">> $f"; "$f"; elif [ -f "$f" ]; then echo ">> bash $f"; bash "$f"; else echo "skip $f"; fi; }
run_if "$ROOT/tools/rc_d2_smoke.sh"
run_if "$ROOT/tools/rc_d3_smoke.sh"
run_if "$ROOT/tools/rc_d4_smoke.sh"
run_if "$ROOT/tools/rc_d5_smoke.sh"
run_if "$ROOT/tools/rc_d6_smoke.sh"
run_if "$ROOT/tools/rc_d7_smoke.sh"
run_if "$ROOT/tools/rc_d8_smoke.sh"
run_if "$ROOT/tools/rc_d9_smoke.sh"
run_if "$ROOT/tools/rc_d10_smoke.sh"
echo "pre-RC4 D2..D10 smoke done"
