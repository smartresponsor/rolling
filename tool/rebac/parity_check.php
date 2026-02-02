#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Infra\Role\Rebac\InMemoryGraphStore;
use App\Service\Role\Rebac\NamespaceConstraint;

// Autoload (direct requires for demo)
require_once __DIR__ . '/../../src/Infra/Role/Rebac/InMemoryGraphStore.php';
require_once __DIR__ . '/../../src/InfraInterface/Role/Rebac/GraphStoreInterface.php';
require_once __DIR__ . '/../../src/Service/Role/Rebac/NamespaceConstraint.php';
require_once __DIR__ . '/../../src/ServiceInterface/Role/Rebac/NamespaceConstraintInterface.php';

@mkdir(__DIR__ . '/../../report', 0775, true);

$store = new InMemoryGraphStore();
$constraints = new NamespaceConstraint([['subject', 'group'], ['group', 'permission'], ['permission', 'resource']], true);

// Tenant t1 graph
$tenant = 't1';
$store->addEdge($tenant, 'subject', 'u1', 'member', 'g1');
$store->addEdge($tenant, 'group', 'g1', 'grants', 'perm.read');
$store->addEdge($tenant, 'permission', 'perm.read', 'allows', 'doc:1');

// direct allow within allowed chain (subject→group→permission→resource)
$ok_same = $store->checkAccess($tenant, 'subject', 'u1', 'allows', 'doc:1', $constraints);

// cross-tenant denial
$tenant2 = 't2';
$store->addEdge($tenant2, 'subject', 'u2', 'member', 'g2');
$store->addEdge($tenant2, 'group', 'g2', 'grants', 'perm.read');
$store->addEdge($tenant2, 'permission', 'perm.read', 'allows', 'doc:2');
$cross_tenant = $store->checkAccess('t1', 'subject', 'u1', 'allows', 'doc:2', $constraints); // should be false

// disallowed namespace hop (subject→permission directly)
$constraints_strict = new NamespaceConstraint([['subject', 'group'], ['group', 'permission'], ['permission', 'resource']], true);
$store->addEdge($tenant, 'subject', 'uX', 'member', 'perm.read'); // odd edge, but hop should be blocked
$bad_hop = $store->checkAccess($tenant, 'subject', 'uX', 'allows', 'doc:1', $constraints_strict); // expect false

$out = [
    'ts' => date('c'),
    'sameTenantAllowed' => $ok_same,
    'crossTenantDenied' => !$cross_tenant,
    'disallowedNamespaceDenied' => !$bad_hop,
    'edges' => [
        't1' => 4,
        't2' => 3,
    ],
];

file_put_contents(__DIR__ . '/../../report/rebac_parity.json', json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "report/rebac_parity.json written\n";
