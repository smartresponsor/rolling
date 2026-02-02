#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
test -f "$ROOT/sdk/java/role/src/main/java/com/smartresponsor/role/Client.java"
if command -v javac >/dev/null 2>&1; then
  javac "$ROOT/sdk/java/role/src/main/java/com/smartresponsor/role/Client.java"
  echo "javac OK"
else
  echo "javac missing (skip)"
fi
echo "RC-D7 Java smoke OK"
