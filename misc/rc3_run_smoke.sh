#!/usr/bin/env bash
set -euo pipefail
cd repo || { echo "repo/ not found"; exit 2; }
if [ -x tools/rc3_smoke.sh ]; then
  bash tools/rc3_smoke.sh
else
  echo "rc3_smoke.sh not found; running available smokes"
  for s in tools/rc*_smoke.sh; do
    [ -f "$s" ] || continue
    echo "--> $s"
    bash "$s" || exit 1
  done
fi
