#!/usr/bin/env bash
set -euo pipefail
php -l src/Model/Role/Rebac/Tuple.php
php -l src/Consistency/Role/Rebac/Token.php
php -l src/Store/Role/Rebac/TupleStoreInterface.php
php -l src/Store/Role/Rebac/InMemoryTupleStore.php
php -l src/Store/Role/Rebac/PdoTupleStore.php
php -l src/Service/Role/Rebac/Writer.php
php -l src/Service/Role/Rebac/Checker.php
php -l src/Http/Role/V2/RebacController.php
php -l bin/role-rebac.php
echo "OK syntax for RC-C2 kit"
