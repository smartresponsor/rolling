#!/usr/bin/env php
<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/Service/Audit/Redactor.php';
require_once __DIR__ . '/../src/Service/Audit/Logger.php';

use App\Service\Audit\Logger;

$repo = realpath(__DIR__ . '/..');
$log = new Logger($repo . '/var/log/role');
$limit = intval($argv[1] ?? 20);
$out = $log->tail($limit);
echo json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
