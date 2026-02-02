#!/usr/bin/env bash
set -euo pipefail
grep -q 'version:' deploy/compose/docker-compose.yaml
echo OK
