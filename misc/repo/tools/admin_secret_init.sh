#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
mkdir -p "$ROOT/var"
if [ -f "$ROOT/var/admin_secret.txt" ]; then echo "exists: $ROOT/var/admin_secret.txt"; exit 0; fi
head -c 32 /dev/urandom | base64 | tr -d '\n' > "$ROOT/var/admin_secret.txt"
echo "created: $ROOT/var/admin_secret.txt"
