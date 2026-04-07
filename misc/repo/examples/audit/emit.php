<?php
require __DIR__ . '/../../src/Service/Audit/Redactor.php';
require __DIR__ . '/../../src/Service/Audit/Logger.php';

use App\Service\Audit\Logger;

$l = new Logger(__DIR__ . '/../../var/log/role');
$event = [
    'trace' => 'demo-1',
    'tenant' => 'acme',
    'subject' => 'user:77',
    'resource' => 'doc:100',
    'relation' => 'editor',
    'context' => ['ip' => '10.0.0.7', 'ssn' => '222-33-4444', 'notes' => 'token_abcd123 x'],
    'effect' => 'deny',
    'reason' => 'not-owner',
];
$obl = [
    'mask' => ['context.ssn'],
    'redact' => [['path' => 'context.notes', 'pattern' => '\\btoken_[a-z0-9]+\\b']],
];
$res = $l->write($event, $obl);
var_dump($res);
