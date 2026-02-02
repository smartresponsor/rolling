#!/usr/bin/env bash
set -euo pipefail
php -l src/Attribute/Role/AttributeService.php
php -l src/Attribute/Role/Provider/AttributeProviderInterface.php
php -l src/Attribute/Role/Provider/UserProvider.php
php -l src/Attribute/Role/Provider/OrgProvider.php
php -l src/Attribute/Role/Provider/ResourceProvider.php
php -l src/Attribute/Role/Cache/ArrayCache.php
echo OK
