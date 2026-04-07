#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infrastructure\Residency\ResidencyStorage;
use App\Service\Residency\StaticResidencyPolicy;

require_once __DIR__ . '/../../src/Infrastructure/Residency/ResidencyStorage.php';
require_once __DIR__ . '/../../src/Service/Residency/StaticResidencyPolicy.php';
require_once __DIR__ . '/../../src/ServiceInterface/Residency/ResidencyPolicyInterface.php';

$tenant = $argv[1] ?? 't1';
$kind = $argv[2] ?? 'policy';
$name = $argv[3] ?? 'policy_v1.php';
$content = $argv[4] ?? "<?php // compiled policy demo\n";

$store = new ResidencyStorage(new StaticResidencyPolicy(['t1' => 'us', 't2' => 'eu']));
$p = $store->write($tenant, $kind, $name, $content);
echo json_encode(['path' => $p], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), "\n";
