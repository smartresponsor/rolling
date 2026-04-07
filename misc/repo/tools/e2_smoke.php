#!/usr/bin/env php
<?php
declare(strict_types=1);
require __DIR__ . '/../src/Http/Role/Api/PelEvalController.php';

use App\Legacy\Http\Api\PelEvalController;
use Symfony\Component\HttpFoundation\Request;

$ctl = new PelEvalController();
$r = $ctl->eval(Request::create('/v2/role/pel-eval', 'POST', [], [], [], [], json_encode([
    'tenant' => 't1', 'subject' => 'u1', 'action' => 'write', 'attrs' => ['role' => 'admin'],
])));
echo $r->getContent(), "\n";
