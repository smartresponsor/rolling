#!/usr/bin/env bash
set -euo pipefail
php -l src/Consistency/Role/TokenSet.php
php -l src/Consistency/Role/Composer.php
php -l src/Cache/Role/ConsistentCachePdpV2.php
php -l src/Http/Role/V2/ConsistencyHeaders.php
echo "OK syntax for RC-C5 kit"
