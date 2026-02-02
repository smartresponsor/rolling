#!/usr/bin/env bash
set -euo pipefail
php -l src/Permission/Role/Model/PermissionDef.php
php -l src/Permission/Role/Catalog/Catalog.php
php -l src/Permission/Role/Catalog/ConfigLoader.php
php -l src/Permission/Role/Catalog/Hasher.php
php -l src/Permission/Role/Catalog/CatalogService.php
php -l src/Http/Role/V2/PermCatalogController.php
php -l config/routes/role_perm_catalog.yaml
echo OK
