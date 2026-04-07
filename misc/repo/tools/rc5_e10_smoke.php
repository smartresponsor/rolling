#!/usr/bin/env php
<?php
declare(strict_types=1);
require __DIR__ . '/../src/Http/Role/Api/SecurityController.php';
require __DIR__ . '/../src/Http/Role/Api/ResidencyController.php';

use App\Legacy\Http\Api\ResidencyController;
use App\Legacy\Http\Api\SecurityController;
use Symfony\Component\HttpFoundation\Request;

$sec = new SecurityController(__DIR__ . '/../var');
$res = new ResidencyController(__DIR__ . '/../config/role/residency.json');

// Sign HS256
$sig = $sec->sign(Request::create('/v2/keys/sign', 'POST', [], [], [], [], json_encode([
    'tenant' => 't1', 'claims' => ['sub' => 'u1', 'role' => 'editor'],
])));
$jwt = json_decode($sig->getContent(), true)['jwt'] ?? '';
echo "JWT: ", $jwt, "\n";

// Verify HS256
$ver = $sec->verify(Request::create('/v2/keys/verify', 'POST', [], [], [], [], json_encode([
    'tenant' => 't1', 'token' => $jwt,
])));
echo "Verify: ", $ver->getContent(), "\n";

// Rotate and sign again
$rot = $sec->rotate(Request::create('/v2/keys/rotate', 'POST', [], [], [], [], json_encode([
    'tenant' => 't1', 'note' => 'smoke-rotate',
])));
echo "Rotate: ", $rot->getContent(), "\n";
$sig2 = $sec->sign(Request::create('/v2/keys/sign', 'POST', [], [], [], [], json_encode([
    'tenant' => 't1', 'claims' => ['sub' => 'u2', 'role' => 'viewer'],
])));
echo "JWT2: ", $sig2->getContent(), "\n";

// Residency check (ok)
$ok = $res->enforce(Request::create('/v2/residency/enforce', 'POST', [], [], [], [], json_encode([
    'tenant' => 't1', 'attrs' => ['region' => 'us'], 'action' => 'read',
])));
echo "Residency ok: ", $ok->getContent(), "\n";

// Residency deny (t2 allows only eu)
$deny = $res->enforce(Request::create('/v2/residency/enforce', 'POST', [], [], [], [], json_encode([
    'tenant' => 't2', 'attrs' => ['region' => 'us'], 'action' => 'read',
])));
echo "Residency deny: ", $deny->getContent(), "\n";
