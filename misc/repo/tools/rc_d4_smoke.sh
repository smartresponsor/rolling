#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
php -l "$ROOT/src/Security/Role/Admin/Roles.php" >/dev/null
php -l "$ROOT/src/Security/Role/Admin/Voter.php" >/dev/null
php -l "$ROOT/src/Security/Role/Keys/KeyStore.php" >/dev/null
php -l "$ROOT/src/Security/Role/Keys/BundleVerifier.php" >/dev/null
php -l "$ROOT/src/Http/Role/Api/JwksController.php" >/dev/null
php -l "$ROOT/src/Http/Role/Api/RotateKeysController.php" >/dev/null
php -l "$ROOT/tools/keys_rotate.php" >/dev/null
bash "$ROOT/tools/admin_secret_init.sh" >/dev/null
php "$ROOT/tools/keys_rotate.php" >/dev/null | grep -q '"ok": true'
echo "RC-D4 smoke OK"
