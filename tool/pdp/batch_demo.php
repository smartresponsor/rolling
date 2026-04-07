#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Service\Pdp\BatchDecision;
use App\Service\Pdp\Dto\DecisionRequest;

require_once __DIR__ . '/../../src/Service/Role/Pdp/BatchDecision.php';
require_once __DIR__ . '/../../src/Service/Role/Pdp/Dto/DecisionRequest.php';
require_once __DIR__ . '/../../src/Service/Role/Pdp/Dto/DecisionResponse.php';

@mkdir(__DIR__ . '/../../report', 0775, true);

$requests = [
    new DecisionRequest(['id' => 'u1', 'roles' => ['reader']], 'read', ['type' => 'doc', 'id' => 'd1', 'ownerId' => 'u2']),
    new DecisionRequest(['id' => 'u2', 'roles' => ['writer']], 'write', ['type' => 'project', 'id' => 'p1', 'ownerId' => 'u2']),
    new DecisionRequest(['id' => 'u3', 'roles' => ['user']], 'delete', ['type' => 'doc', 'id' => 'd2', 'ownerId' => 'u3']),
    new DecisionRequest(['id' => 'admin', 'roles' => ['admin']], 'delete', ['type' => 'doc', 'id' => 'd3', 'ownerId' => 'uX']),
];

$engine = new BatchDecision();
$responses = $engine->decideMany($requests);

$out = [
    'ts' => date('c'),
    'count' => count($responses),
    'items' => array_map(fn($r) => $r->toArray(), $responses),
];
file_put_contents(__DIR__ . '/../../report/pdp_batch_demo.json', json_encode($out, JSON_PRETTY_PRINT));
echo "report/pdp_batch_demo.json written\n";
