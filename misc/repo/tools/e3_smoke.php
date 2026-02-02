#!/usr/bin/env php
<?php
declare(strict_types=1);
require __DIR__ . '/../src/Http/Role/Api/PolicyController.php';

use Http\Role\Api\PolicyController;
use Symfony\Component\HttpFoundation\Request;

$ctl = new PolicyController();
$ctl->putDraft(Request::create('/', 'POST', [], [], [], [], json_encode(['tenant' => 't1', 'expr' => "(subject.role in ['admin']) and action == 'write'"])));
echo $ctl->publish(Request::create('/', 'POST', [], [], [], [], json_encode(['tenant' => 't1', 'note' => 'init'])))->getContent(), "\n";
echo $ctl->getEffective(Request::create('/', 'GET', ['tenant' => 't1']))->getContent(), "\n";
