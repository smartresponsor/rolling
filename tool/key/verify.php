#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infrastructure\Key\FileKeyProvider;
use App\Legacy\Service\Security\HmacSigner;

require_once __DIR__ . '/../../src/Infrastructure/Key/FileKeyProvider.php';
require_once __DIR__ . '/../../src/Service/Role/Security/HmacSigner.php';
require_once __DIR__ . '/../../src/ServiceInterface/Key/KeyProviderInterface.php';

$tenant = $argv[1] ?? 't1';
$payload = $argv[2] ?? 'example';
$kid = $argv[3] ?? '';
$sig = $argv[4] ?? '';

$signer = new HmacSigner(new FileKeyProvider());
$ok = $signer->verify($tenant, $payload, $kid, $sig);
echo json_encode(['ok' => $ok], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
