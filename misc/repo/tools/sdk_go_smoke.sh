#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
test -f "$ROOT/sdk/go/role/client.go"
if command -v go >/dev/null 2>&1; then
  (cd "$ROOT/sdk/go/role" && go build ./...)
  echo "go build OK"
else
  echo "go missing (skip)"
fi
echo "RC-D7 Go smoke OK"
