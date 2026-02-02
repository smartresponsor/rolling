#!/usr/bin/env bash
set -euo pipefail
ROOT="$(pwd)"
REPORT_DIR="$ROOT/report"; mkdir -p "$REPORT_DIR"
TXT="$REPORT_DIR/sdk_sanity.txt"
echo "== SDK sanity == $(date -u +'%Y-%m-%dT%H:%M:%SZ')" > "$TXT"

PHP_OK=false
TS_OK=false

echo "[PHP] running..." | tee -a "$TXT"
set +e
php examples/php/check.php > "$REPORT_DIR/php_sdk.json" 2>>"$TXT"
PHP_CODE=$?
set -e
if [ $PHP_CODE -eq 0 ] && grep -q '"decision"' "$REPORT_DIR/php_sdk.json"; then
  echo "[PHP] PASS" | tee -a "$TXT"; PHP_OK=true
else
  echo "[PHP] FAIL (exit=$PHP_CODE)" | tee -a "$TXT"
fi

if command -v node >/dev/null 2>&1; then
  echo "[TS] running..." | tee -a "$TXT"
  pushd examples/js >/dev/null
  npm i >/dev/null 2>&1 || true
  set +e
  node --loader ts-node/esm ../js/check.ts > "$REPORT_DIR/ts_sdk.json" 2>>"$TXT"
  TS_CODE=$?
  set -e
  popd >/dev/null
  if [ ${TS_CODE:-1} -eq 0 ] && grep -q '"decision"' "$REPORT_DIR/ts_sdk.json"; then
    echo "[TS] PASS" | tee -a "$TXT"; TS_OK=true
  else
    echo "[TS] FAIL (exit=${TS_CODE:-1})" | tee -a "$TXT"
  fi
else
  echo "[TS] skipped — node not found" | tee -a "$TXT"
fi

if [ "$PHP_OK" = true ] && [ "$TS_OK" = true ]; then
  echo "ACCEPT: true" | tee -a "$TXT"; exit 0
else
  echo "ACCEPT: false" | tee -a "$TXT"; exit 1
fi
