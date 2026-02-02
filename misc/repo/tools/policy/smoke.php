#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infra\Role\Policy\InMemoryGrantRepository;
use App\Service\Role\Policy\PolicyEngine;
use App\Service\Role\Policy\Voter\AttributeVoter;
use App\Service\Role\Policy\Voter\RoleVoter;
use App\Service\Role\Policy\Voter\TenantBoundaryVoter;

// requires
require_once __DIR__ . '/../../src/Service/Role/Policy/Decision.php';
require_once __DIR__ . '/../../src/ServiceInterface/Role/Policy/VoterInterface.php';
require_once __DIR__ . '/../../src/ServiceInterface/Role/Policy/PolicyEngineInterface.php';
require_once __DIR__ . '/../../src/Service/Role/Policy/PolicyEngine.php';
require_once __DIR__ . '/../../src/Service/Role/Policy/Voter/RoleVoter.php';
require_once __DIR__ . '/../../src/Service/Role/Policy/Voter/AttributeVoter.php';
require_once __DIR__ . '/../../src/Service/Role/Policy/Voter/TenantBoundaryVoter.php';
require_once __DIR__ . '/../../src/Infra/Role/Policy/InMemoryGrantRepository.php';

$reportDir = __DIR__ . '/../../report';
@mkdir($reportDir, 0775, true);

$repo = new InMemoryGrantRepository();
$repo->loadFromNdjson(__DIR__ . '/../../examples/grants_policy.ndjson');

$engine = new PolicyEngine('affirmative');
$engine->addVoter(new TenantBoundaryVoter());
$engine->addVoter(new RoleVoter($repo));
$engine->addVoter(new AttributeVoter());

$scenarios = [
    ['id' => 'S1', 'subject' => ['id' => 'x', 'roles' => ['admin'], 'tenant' => 't1'], 'action' => 'can_read', 'resource' => ['type' => 'order', 'id' => 'o1', 'tenant' => 't1'], 'ctx' => ['tenant' => 't1']],
    ['id' => 'S2', 'subject' => ['id' => 'u2', 'roles' => [], 'tenant' => 't1'], 'action' => 'can_write', 'resource' => ['type' => 'order', 'id' => 'o2', 'tenant' => 't1', 'ownerId' => 'u2'], 'ctx' => ['tenant' => 't1']],
    ['id' => 'S3', 'subject' => ['id' => 'u3', 'roles' => ['guest'], 'tenant' => 't1'], 'action' => 'can_read', 'resource' => ['type' => 'order', 'id' => 'o1', 'tenant' => 't1'], 'ctx' => ['tenant' => 't1']],
    ['id' => 'S4', 'subject' => ['id' => 'u4', 'roles' => ['admin'], 'tenant' => 't2'], 'action' => 'can_read', 'resource' => ['type' => 'order', 'id' => 'o1', 'tenant' => 't1'], 'ctx' => ['tenant' => 't1']], // tenant mismatch -> DENY
];

$out = [];
foreach ($scenarios as $s) {
    $dec = $engine->decide($s['subject'], $s['action'], $s['resource'], $s['ctx']);
    $out[] = ['id' => $s['id'], 'allowed' => $dec->allowed, 'meta' => $dec->meta];
}

file_put_contents($reportDir . '/policy_smoke.json', json_encode(['created' => date('c'), 'strategy' => $engine->getStrategy(), 'scenarios' => $out], JSON_PRETTY_PRINT));
echo "policy_smoke.json written\n";
