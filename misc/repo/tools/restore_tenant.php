#!/usr/bin/env php
<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/Service/Role/Tenant/Restore.php';

use Tenant\Restore;

$path = $argv[1] ?? null;
if (!$path || !file_exists($path)) {
    fwrite(STDERR, "Usage: php tools/restore_tenant.php <path.zip>\n");
    exit(2);
}
$rt = new Restore(__DIR__ . '/../var');
$res = $rt->run($path);
echo json_encode($res, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
