#!/usr/bin/env bash
set -euo pipefail
ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
# PHP lint
php -l "$ROOT/src/Http/Role/SymfonyBundle/RoleBundle.php" >/dev/null
php -l "$ROOT/src/Http/Role/SymfonyBundle/DependencyInjection/Configuration.php" >/dev/null
php -l "$ROOT/src/Http/Role/SymfonyBundle/DependencyInjection/RoleExtension.php" >/dev/null
php -l "$ROOT/src/Http/Role/SymfonyBundle/Resources/config/services.php" >/dev/null
php -l "$ROOT/src/Http/Role/Client.php" >/dev/null
# Go source presence
test -f "$ROOT/src/Http/Role/Adapters/Envoy/main.go"
test -f "$ROOT/src/Http/Role/Adapters/Envoy/go.mod"
# Compose files presence
test -f "$ROOT/deploy/integrations/docker-compose.yml"
test -f "$ROOT/deploy/integrations/envoy/envoy.yaml"
echo "RC-D6 smoke OK (static checks)"
