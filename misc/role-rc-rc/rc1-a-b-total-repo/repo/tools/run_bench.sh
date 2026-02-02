#!/usr/bin/env bash
set -euo pipefail
ROOT="$(pwd)"
chmod +x bin/role-bench.php
php bin/role-bench.php | tee report/bench/run_stdout.txt
echo "Done."
