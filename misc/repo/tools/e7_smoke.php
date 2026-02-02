#!/usr/bin/env php
<?php
declare(strict_types=1);
require __DIR__ . '/../src/Http/Role/Api/WhatIfController.php';

use Http\Role\Api\WhatIfController;
use Symfony\Component\HttpFoundation\Request;

$ctl = new WhatIfController();
$r = $ctl->run(Request::create('/', 'POST', [], [], [], [], json_encode([
    'tenant' => 't1', 'subject' => 'u1', 'action' => 'read', 'attrs' => ['role' => 'viewer'], 'hyp' => ['role' => 'admin'],
])));
echo $r->getContent(), "\n";
