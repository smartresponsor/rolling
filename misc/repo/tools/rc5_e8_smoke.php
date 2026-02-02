#!/usr/bin/env php
<?php
declare(strict_types=1);
require __DIR__ . '/../src/Http/Role/Api/ObligationController.php';

use Http\Role\Api\ObligationController;
use Symfony\Component\HttpFoundation\Request;

$ctl = new ObligationController(__DIR__ . '/../var');

$req = Request::create('/v2/obligations/apply', 'POST', [], [], [], [], json_encode([
    'tenant' => 't1',
    'relation' => 'viewer',
    'decision' => ['allowed' => true],
    'attrs' => ['region' => 'EU'],
    'resource' => ['ssn' => '123-45-6789', 'secret' => 'TOP', 'name' => 'John'],
]));
echo $ctl->apply($req)->getContent(), "\n";
