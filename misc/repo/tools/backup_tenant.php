#!/usr/bin/env php
<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/Service/Role/Tenant/Backup.php';

use App\Service\Tenant\Backup;

$tenant = $argv[1] ?? null;
if (!$tenant) {
    fwrite(STDERR, "Usage: php tools/backup_tenant.php <tenant>\n");
    exit(2);
}
$bk = new Backup(__DIR__ . '/../var', __DIR__ . '/../var/backup');
$res = $bk->run($tenant);
echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
