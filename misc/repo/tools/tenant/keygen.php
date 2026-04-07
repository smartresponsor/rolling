#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infrastructure\Tenant\InMemoryTenantKeyRepository;
use App\Service\Tenant\TenantKeyProvider;

require_once __DIR__ . '/../../src/Infra/Role/Tenant/InMemoryTenantKeyRepository.php';
require_once __DIR__ . '/../../src/Service/Role/Tenant/TenantKeyProvider.php';

$reportDir = __DIR__ . '/../../report';
@mkdir($reportDir, 0775, true);

$repo = new InMemoryTenantKeyRepository();
$prov = new TenantKeyProvider($repo);

// Read list
$path = __DIR__ . '/../../examples/tenant_list.ndjson';
$fh = fopen($path, 'r');
$tenants = [];
while (($line = fgets($fh)) !== false) {
    $row = json_decode($line, true);
    if (isset($row['tenant'])) {
        $tenants[] = $row['tenant'];
    }
}
fclose($fh);

$out = [];
foreach ($tenants as $t) {
    $key = $prov->rotate($t);
    $out[$t] = $key;
}
file_put_contents($reportDir . '/tenant_keys.json', json_encode($out, JSON_PRETTY_PRINT));
echo "tenant_keys.json written\n";
