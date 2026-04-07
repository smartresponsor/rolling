#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infrastructure\Rebac\NullRebacClient;
use App\Service\Pdp\Policy\InMemoryPolicyProvider;
use App\Service\Pdp\Policy\TupleMapper;

require_once __DIR__ . '/../../src/Service/Role/Pdp/Policy/InMemoryPolicyProvider.php';
require_once __DIR__ . '/../../src/Service/Role/Pdp/Policy/TupleMapper.php';
require_once __DIR__ . '/../../src/Infra/Role/Rebac/Tuple.php';
require_once __DIR__ . '/../../src/Infra/Role/Rebac/NullRebacClient.php';

// Build grants -> tuples -> load into Null client
$grants = [
    ['subjectRole' => 'admin', 'action' => 'can_read', 'resourceType' => 'order', 'resourceId' => 'o1', 'tenant' => 't1'],
    ['subjectId' => 'u2', 'action' => 'can_write', 'resourceType' => 'order', 'resourceId' => 'o2', 'tenant' => 't1'],
];
$tuples = TupleMapper::toTuples($grants);
$rebac = new NullRebacClient();
$rebac->writeTuples($tuples);

// PDP setup mirroring grants
$provider = new InMemoryPolicyProvider();
$provider->addRule('admin_read_order', ['role' => 'admin', 'action' => 'can_read', 'resource' => 'order', 'tenant' => 't1']);
$provider->addRule('u2_write_order', ['action' => 'can_write', 'resource' => 'order', 'tenant' => 't1']);

// Scenarios
$scenarios = [
    [['id' => 'x', 'roles' => ['admin']], 'can_read', ['type' => 'order', 'id' => 'o1'], ['tenant' => 't1']],
    [['id' => 'u2', 'roles' => []], 'can_write', ['type' => 'order', 'id' => 'o2'], ['tenant' => 't1']],
    [['id' => 'u3', 'roles' => ['guest']], 'can_read', ['type' => 'order', 'id' => 'o1'], ['tenant' => 't1']],
];

$ok = 0;
$total = count($scenarios);
foreach ($scenarios as $s) {
    [$sub, $rel, $obj, $ctx] = $s;
    $pdp = $provider->isAllowed($sub, $rel, $obj, $ctx);
    $reb = $rebac->check(['type' => 'user', 'id' => $sub['id']], $rel, $obj, $ctx);
    if ($pdp === $reb) $ok++;
}
$ratio = $total ? ($ok / $total) : 1.0;
@mkdir(__DIR__ . '/../../report', 0775, true);
file_put_contents(__DIR__ . '/../../report/rebac_parity.json', json_encode(['ok' => $ok, 'total' => $total, 'ratio' => $ratio], JSON_PRETTY_PRINT));
echo "Parity: {$ok}/{$total} (" . round($ratio * 100, 1) . "%) -> report/rebac_parity.json\n";
