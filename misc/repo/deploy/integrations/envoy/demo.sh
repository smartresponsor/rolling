#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")"
docker compose up -d --build
echo "Allow (no deny header):"
curl -sS -i http://localhost:8080/get | sed -n '1,12p'
echo
echo "Deny (with x-role-debug-deny):"
curl -sS -i -H 'x-role-debug-deny: 1' http://localhost:8080/get | sed -n '1,12p'
