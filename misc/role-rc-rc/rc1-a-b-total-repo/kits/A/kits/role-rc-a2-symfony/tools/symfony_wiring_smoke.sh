#!/usr/bin/env bash
set -euo pipefail
php -l src/Integration/Symfony/Controller/MetricsController.php
echo "OK: MetricsController syntax"
