#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infrastructure\Tenant\InMemoryTenantKeyRepository;
use App\Legacy\Service\Security\HmacSigner;
use App\Service\Tenant\TenantKeyProvider;

require_once __DIR__ . '/../../src/Infra/Role/Tenant/InMemoryTenantKeyRepository.php';
require_once __DIR__ . '/../../src/Service/Role/Tenant/TenantKeyProvider.php';
require_once __DIR__ . '/../../src/Service/Role/Security/Base64Url.php';
require_once __DIR__ . '/../../src/Service/Role/Security/HmacSigner.php';

$reportDir = __DIR__ . '/../../report';
@mkdir($reportDir, 0775, true);

$repo = new InMemoryTenantKeyRepository();
$prov = new TenantKeyProvider($repo);
$signer = new HmacSigner('sha256');

$t1 = 't1';
$t2 = 't2';
$k1 = $prov->rotate($t1);
$k2 = $prov->rotate($t2);

$payload = 'hello-role-component';

$sig1 = $signer->sign($payload, $k1);
$ok_same = $signer->verify($payload, $sig1, $k1);
$ok_cross = $signer->verify($payload, $sig1, $k2); // must be false

file_put_contents($reportDir . '/sign_verify.json', json_encode([
    'payload' => $payload,
    't1_key' => $k1,
    't2_key' => $k2,
    'signature' => $sig1,
    'verify_same' => $ok_same,
    'verify_cross' => $ok_cross,
], JSON_PRETTY_PRINT));

echo "sign_verify.json written (same=" . ($ok_same ? 'true' : 'false') . ", cross=" . ($ok_cross ? 'true' : 'false') . ")\n";
