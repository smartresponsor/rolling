#!/usr/bin/env bash
set -euo pipefail
php -l src/Security/Role/Util/Base64Url.php
php -l src/Security/Role/Policy/PolicySigner.php
php -l src/Security/Role/Policy/PolicyVerifier.php
php -l src/Security/Role/Hmac/Canonicalizer.php
php -l src/Security/Role/Hmac/Signer.php
php -l src/Security/Role/Hmac/Verifier.php
php -l src/Http/Role/Security/HmacGuardSubscriber.php
echo OK
