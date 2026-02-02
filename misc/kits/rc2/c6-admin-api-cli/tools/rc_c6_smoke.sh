#!/usr/bin/env bash
set -euo pipefail
php -l src/Security/Role/Admin/AdminTokenGuard.php
php -l src/Metrics/Role/Admin/AdminMetrics.php
php -l src/Service/Role/Admin/RebacStatsService.php
php -l src/Http/Role/V2/AdminPolicyController.php
php -l src/Http/Role/V2/AdminRebacController.php
php -l bin/role-admin.php
echo "OK syntax for RC-C6 kit"
