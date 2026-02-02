#!/usr/bin/env bash
set -euo pipefail
# PHP
php -l sdk/php/src/Http/ResponseErrorMapper.php
php -l sdk/php/src/Exception/ApiException.php
php -l sdk/php/src/Exception/BadRequestException.php
php -l sdk/php/src/Exception/UnauthorizedException.php
php -l sdk/php/src/Exception/ForbiddenException.php
php -l sdk/php/src/Exception/RateLimitException.php
php -l sdk/php/src/Exception/RemoteErrorException.php
echo "PHP SDK exception wiring OK"

# TS (проверяем наличие конфигов)
test -f sdk/js/tsconfig.build.json && echo "TS build config present"
echo "Done."
