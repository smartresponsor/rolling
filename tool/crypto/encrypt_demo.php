#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infra\Role\Key\FileKeyProvider;
use App\Service\Role\Security\SimpleEncryptor;

require_once __DIR__ . '/../../src/Infra/Role/Key/FileKeyProvider.php';
require_once __DIR__ . '/../../src/Service/Role/Security/SimpleEncryptor.php';
require_once __DIR__ . '/../../src/ServiceInterface/Role/Key/KeyProviderInterface.php';

$tenant = $argv[1] ?? 't1';
$data = $argv[2] ?? 'secret';

$enc = new SimpleEncryptor(new FileKeyProvider());
$pack = $enc->encrypt($tenant, $data);
$pt = $enc->decrypt($tenant, $pack['kid'], $pack['iv'], $pack['ct'], $pack['tag']);

echo json_encode(['roundtrip' => ($pt === $data), 'pack' => $pack], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
