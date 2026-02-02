#!/usr/bin/env bash
set -euo pipefail
php -l src/Policy/Role/Opa/InputBuilder.php
php -l src/Net/Role/Opa/OpaClientInterface.php
php -l src/Net/Role/Opa/OpaHttpClient.php
php -l src/Policy/Role/Opa/OpaPdpV2.php
echo "OK syntax for RC-C4 kit"
