#!/usr/bin/env php
<?php
declare(strict_types=1);
require __DIR__ . '/../src/Http/Role/Api/SodController.php';

use App\Legacy\Http\Api\SodController;
use Symfony\Component\HttpFoundation\Request;

$ctl = new SodController();
$r1 = $ctl->check(Request::create('/', 'POST', [], [], [], [], json_encode(['attrs' => ['requester' => 'u1', 'approver' => 'u1']])));
echo $r1->getContent(), "\n";
$r2 = $ctl->check(Request::create('/', 'POST', [], [], [], [], json_encode(['attrs' => ['requester' => 'u1', 'approver' => 'u2', 'approverNeed' => 2, 'approverHave' => 1]])));
echo $r2->getContent(), "\n";
