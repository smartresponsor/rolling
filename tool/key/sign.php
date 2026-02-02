#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infra\Role\Key\FileKeyProvider;
use Service\Role\Security\HmacSigner;

require_once __DIR__ . '/../../src/Infra/Role/Key/FileKeyProvider.php';
require_once __DIR__ . '/../../src/Service/Role/Security/HmacSigner.php';
require_once __DIR__ . '/../../src/ServiceInterface/Role/Key/KeyProviderInterface.php';

$tenant = $argv[1] ?? 't1';
$payload = $argv[2] ?? 'example';

$signer = new HmacSigner(new FileKeyProvider());
$out = $signer->sign($tenant, $payload);
echo json_encode(['tenant' => $tenant, 'kid' => $out['kid'], 'sig' => $out['sig']], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
