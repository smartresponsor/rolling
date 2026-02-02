#!/usr/bin/env php
<?php
declare(strict_types=1);
require __DIR__ . '/../src/Http/Role/Api/ExplainController.php';

use Http\Role\Api\ExplainController;
use Symfony\Component\HttpFoundation\Request;

$ctl = new ExplainController();
$r = $ctl->explain(Request::create('/', 'POST', [], [], [], [], json_encode(['tenant' => 't1', 'subject' => 'u1', 'action' => 'read'])));
echo $r->getContent(), "\n";
