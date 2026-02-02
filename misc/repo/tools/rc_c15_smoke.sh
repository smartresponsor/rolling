#!/usr/bin/env bash
set -euo pipefail
php -l src/Http/Role/V2/Bulk/BulkReaderInterface.php
php -l src/Http/Role/V2/Bulk/NdjsonReader.php
php -l src/Http/Role/V2/Bulk/CsvReader.php
php -l src/Http/Role/V2/Bulk/NdjsonWriter.php
php -l src/Http/Role/V2/BulkController.php
php -l config/routes/role_bulk.yaml
echo OK
