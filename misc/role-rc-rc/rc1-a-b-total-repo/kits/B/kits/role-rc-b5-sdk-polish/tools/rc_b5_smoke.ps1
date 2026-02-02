Param()
php -l sdk/php/src/Http/ResponseErrorMapper.php
php -l sdk/php/src/Exception/ApiException.php
php -l sdk/php/src/Exception/BadRequestException.php
php -l sdk/php/src/Exception/UnauthorizedException.php
php -l sdk/php/src/Exception/ForbiddenException.php
php -l sdk/php/src/Exception/RateLimitException.php
php -l sdk/php/src/Exception/RemoteErrorException.php
Write-Host "PHP SDK exception wiring OK"
if (Test-Path sdk/js/tsconfig.build.json)
{
    Write-Host "TS build config present"
}
