#!/usr/bin/env php
<?php
declare(strict_types=1);
require __DIR__ . '/../src/Http/Role/Api/EvalController.php';

use App\Legacy\Http\Api\EvalController;
use Symfony\Component\HttpFoundation\Request;

$ctl = new EvalController();
$r = $ctl->eval(Request::create('/v2/role/eval', 'POST', [], [], [], [], json_encode(['tenant' => 't1', 'subject' => 'u1', 'action' => 'read'])));
echo $r->getContent(), "\n";
