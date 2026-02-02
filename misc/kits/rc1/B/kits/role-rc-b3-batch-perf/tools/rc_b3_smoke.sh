#!/usr/bin/env bash
set -euo pipefail
php -l src/Policy/Role/Batch/CheckBatchProcessor.php
php -l src/Http/Role/V2/ApiV2BatchStream.php
php -l bin/role-batch-perf.php
echo "OK syntax for RC-B3 kit"
