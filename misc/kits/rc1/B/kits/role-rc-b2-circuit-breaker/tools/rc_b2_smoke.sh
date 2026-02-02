#!/usr/bin/env bash
set -euo pipefail
php -l src/Resilience/Role/CircuitBreakingPdpV2.php
php -l src/Resilience/Role/Clock.php
php -l src/Net/Http/RemoteHttpException.php
echo "OK syntax for RC-B2 kit"
