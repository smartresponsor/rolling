#!/usr/bin/env php
<?php
declare(strict_types=1);
require __DIR__ . '/../src/Http/Role/Api/ContextController.php';

use App\Legacy\Http\Api\ContextController;
use Symfony\Component\HttpFoundation\Request;

putenv('ROLE_SUBJECT=u42');
$ctl = new ContextController();
$r = $ctl->capture(Request::create('/', 'POST', [], [], [], ['HTTP_X_ROLE' => 'editor', 'HTTP_X_REGION' => 'eu'], ''));
echo $r->getContent(), "\n";
