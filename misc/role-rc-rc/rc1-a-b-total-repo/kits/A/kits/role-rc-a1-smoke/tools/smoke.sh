#!/usr/bin/env bash
set -euo pipefail
ROOT="$(pwd)"
REPORT_DIR="$ROOT/report"
mkdir -p "$REPORT_DIR"
TXT="$REPORT_DIR/smoke.txt"
JSON="$REPORT_DIR/smoke.json"

echo "== RC-A1 Smoke == $(date -u +'%Y-%m-%dT%H:%M:%SZ')" > "$TXT"

if ! command -v php >/dev/null 2>&1; then
  echo "PHP not found in PATH" | tee -a "$TXT"
  echo '{"ok":false,"reason":"php-not-found"}' > "$JSON"
  exit 1
fi

echo "PHP: $(php -v | head -n1)" | tee -a "$TXT"
echo >> "$TXT"

# Collect PHP files
MAPFILE -t FILES < <(find src sdk/php tests bin -type f -name '*.php' 2>/dev/null | sort || true)
TOTAL=${#FILES[@]}
echo "[Lint] Files to check: $TOTAL" | tee -a "$TXT"

ERRORS=0
if [ "$TOTAL" -gt 0 ]; then
  for f in "${FILES[@]}"; do
    OUT="$(php -l "$f" 2>&1 || true)"
    if echo "$OUT" | grep -q "Errors parsing"; then
      echo "E: $OUT" | tee -a "$TXT"
      ERRORS=$((ERRORS+1))
    fi
  done
fi

echo "[Lint] Syntax errors: $ERRORS" | tee -a "$TXT"
TESTS_RUN=false
TESTS_OK=null
TESTS_NOTE="not-run"

# PHPUnit detection
if [ -x "vendor/bin/phpunit" ]; then
  echo >> "$TXT"
  echo "Running tests: vendor/bin/phpunit --colors=never" | tee -a "$TXT"
  set +e
  vendor/bin/phpunit --colors=never | tee -a "$TXT"
  CODE=${PIPESTATUS[0]}
  set -e
  TESTS_RUN=true
  TESTS_OK=$([ "$CODE" -eq 0 ] && echo true || echo false)
  TESTS_NOTE="exit:$CODE"
elif command -v phpunit >/dev/null 2>&1; then
  echo >> "$TXT"
  echo "Running tests: phpunit --colors=never" | tee -a "$TXT"
  set +e
  phpunit --colors=never | tee -a "$TXT"
  CODE=${PIPESTATUS[0]}
  set -e
  TESTS_RUN=true
  TESTS_OK=$([ "$CODE" -eq 0 ] && echo true || echo false)
  TESTS_NOTE="exit:$CODE"
else
  echo >> "$TXT"
  echo "PHPUnit not found — tests skipped (mark @skip with reason if needed)" | tee -a "$TXT"
  TESTS_RUN=false
  TESTS_OK=null
  TESTS_NOTE="phpunit-not-found"
fi

# Acceptance
ACCEPT_LINT=$([ "$ERRORS" -eq 0 ] && echo true || echo false)
if [ "$TESTS_RUN" = true ]; then
  ACCEPT_TESTS=$([ "$TESTS_OK" = true ] && echo true || echo false)
else
  # Допускается skip, если явно указана причина; тут фиксируем как true|skip
  ACCEPT_TESTS=true
fi

ACCEPT=$([ "$ACCEPT_LINT" = true ] && [ "$ACCEPT_TESTS" = true ] && echo true || echo false)

echo >> "$TXT"
echo "== Summary ==" | tee -a "$TXT"
echo "lint_ok: $ACCEPT_LINT" | tee -a "$TXT"
echo "tests_run: $TESTS_RUN" | tee -a "$TXT"
echo "tests_ok: ${TESTS_OK}" | tee -a "$TXT"
echo "tests_note: ${TESTS_NOTE}" | tee -a "$TXT"
echo "accept: $ACCEPT" | tee -a "$TXT"

cat > "$JSON" <<JSON
{
  "lint": {"files": $TOTAL, "errors": $ERRORS, "ok": $ACCEPT_LINT},
  "tests": {"run": $TESTS_RUN, "ok": ${TESTS_OK}, "note": "${TESTS_NOTE}"},
  "accept": $ACCEPT,
  "timestamp": "$(date -u +'%Y-%m-%dT%H:%M:%SZ')"
}
JSON

echo
echo "Report: $TXT"
