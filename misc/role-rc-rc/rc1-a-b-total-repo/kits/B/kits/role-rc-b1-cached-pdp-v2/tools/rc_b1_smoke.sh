#!/usr/bin/env bash
set -euo pipefail
php -l src/Cache/Role/KeyValueCache.php
php -l src/Cache/Role/InMemoryCache.php
php -l src/Cache/Role/Psr16CacheAdapter.php
php -l src/Invalidation/Role/SubjectEpochs.php
php -l src/Policy/Role/Decorator/V2/CachedPdpV2.php
echo "OK syntax for RC-B1 kit"
