#!/usr/bin/env bash
set -euo pipefail
php -l bench/lib/Timer.php
php -l bench/lib/Stats.php
php -l bench/lib/Ndjson.php
echo OK
