#!/usr/bin/env php
<?php
declare(strict_types=1);
require_once __DIR__ . '/../src/Security/Role/Keys/KeyStore.php';

use src\Security\Role\Keys\KeyStore;

$dir = $argv[1] ?? __DIR__ . '/../var/keys';
$keys = new KeyStore($dir);
$res = $keys->rotate();
echo json_encode(['ok' => true] + $res, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
