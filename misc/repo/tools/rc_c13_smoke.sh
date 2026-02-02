#!/usr/bin/env bash
set -euo pipefail
php -l src/Http/Role/V2/ContextEnricherSubscriber.php
php -l src/Http/Role/V2/Context/ContextMerge.php
echo OK
