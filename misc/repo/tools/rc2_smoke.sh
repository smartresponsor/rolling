#!/usr/bin/env bash
set -euo pipefail
# PHP syntax across repo
if command -v fd >/dev/null 2>&1; then
  files=$(fd -e php . src bin config || true)
else
  files=$(find src bin config -type f -name "*.php" 2>/dev/null || true)
fi
for f in $files; do php -l "$f" >/dev/null; done
echo "[rc2] php -l OK"
# Per-kit smokes if present
for s in tools/rc_c2_smoke.sh tools/rc_c3_smoke.sh tools/rc_c4_smoke.sh tools/rc_c5_smoke.sh tools/rc_c6_smoke.sh tools/rc_c7_smoke.sh; do
  if [[ -x "$s" ]]; then echo "[rc2] run $s"; "$s"; fi
done
echo "[rc2] all smokes done"
