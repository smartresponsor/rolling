#!/usr/bin/env php
<?php
declare(strict_types=1);
require __DIR__ . '/../src/Http/Role/Api/AdminController.php';

use App\Legacy\Http\Api\AdminController;
use Symfony\Component\HttpFoundation\Request;

$ctl = new AdminController(__DIR__ . '/../var');

// start approval (need 2 approvals)
$start = $ctl->start(Request::create('/v2/admin/approval/start', 'POST', [], [], [], [], json_encode([
    'tenant' => 't1', 'relation' => 'policy-change', 'resource' => 'policy:active', 'requester' => 'user:req',
    'opts' => ['required' => 2],
])));
echo $start->getContent(), "\n";
$id = json_decode($start->getContent(), true)['id'] ?? '';

// approve by boss
echo $ctl->approve(Request::create('/v2/admin/approval/approve', 'POST', [], [], [], [], json_encode([
    'id' => $id, 'subject' => 'user:boss', 'comment' => 'LGTM',
])))->getContent(), "\n";

// delegate 'sec' to 'assistant', then approve as assistant (delegated)
echo $ctl->delegate(Request::create('/v2/admin/delegate', 'POST', [], [], [], [], json_encode([
    'tenant' => 't1', 'from' => 'user:sec', 'to' => 'user:assistant', 'until' => time() + 3600,
])))->getContent(), "\n";

echo $ctl->approve(Request::create('/v2/admin/approval/approve', 'POST', [], [], [], [], json_encode([
    'id' => $id, 'subject' => 'user:assistant', 'comment' => 'on behalf of sec',
])))->getContent(), "\n";

// override (noop since already approved)
echo $ctl->override(Request::create('/v2/admin/override', 'POST', [], [], [], [], json_encode([
    'id' => $id, 'actor' => 'user:cto', 'reason' => 'emergency',
])))->getContent(), "\n";
