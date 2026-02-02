#!/usr/bin/env bash
set -euo pipefail
MSG="${1:-role: apply changes}"
git add -A
git commit -m "$MSG" || true
