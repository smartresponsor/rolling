#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infrastructure\Key\FileKeyProvider;

require_once __DIR__ . '/../../src/Infrastructure/Key/FileKeyProvider.php';
require_once __DIR__ . '/../../src/ServiceInterface/Key/KeyProviderInterface.php';

$tenant = $argv[1] ?? 't1';
$kp = new FileKeyProvider();
$k = $kp->rotate($tenant);
echo json_encode(['kid' => $k['kid']], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
