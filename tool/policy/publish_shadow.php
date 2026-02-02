#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Service\Role\Policy\PelCompiler;

require_once __DIR__ . '/../../src/Service/Role/Policy/PelCompiler.php';

@mkdir(__DIR__ . '/../../report', 0775, true);

$config = file_get_contents(__DIR__ . '/../../config/packages/role_policy.yaml') ?: '';
$active = null;
$shadow = null;
foreach (explode("\n", $config) as $ln) {
    if (preg_match('/active:\s*(.+)$/', trim($ln), $m)) $active = trim($m[1]);
    if (preg_match('/shadow:\s*(.+)$/', trim($ln), $m)) $shadow = trim($m[1]);
}
if (!$active || !$shadow) {
    fwrite(STDERR, "config missing active/shadow\n");
    exit(2);
}

$compiler = new PelCompiler();
$activePath = $compiler->compile('policy_active', __DIR__ . '/../src/' . $active, __DIR__ . '/../../var/policy_compiled');
$shadowPath = $compiler->compile('policy_shadow', __DIR__ . '/../src/' . $shadow, __DIR__ . '/../../var/policy_compiled');

$activeFn = require $activePath;
$shadowFn = require $shadowPath;

// Demo batch of inputs
$cases = [
    ['subject' => ['id' => 'u1', 'roles' => ['reader']], 'action' => 'read', 'resource' => ['type' => 'doc', 'ownerId' => 'u2'], 'context' => []],
    ['subject' => ['id' => 'u2', 'roles' => ['writer']], 'action' => 'write', 'resource' => ['type' => 'project', 'ownerId' => 'u2'], 'context' => []],
    ['subject' => ['id' => 'u3', 'roles' => ['user']], 'action' => 'delete', 'resource' => ['type' => 'doc', 'ownerId' => 'u3'], 'context' => []],
    ['subject' => ['id' => 'root', 'roles' => ['admin']], 'action' => 'delete', 'resource' => ['type' => 'doc', 'ownerId' => 'x'], 'context' => []],
];

$outFile = __DIR__ . '/../../report/policy_shadow_' . date('Ymd_His') . '.ndjson';
$fh = fopen($outFile, 'w');
foreach ($cases as $c) {
    $a = $activeFn($c['subject'], $c['action'], $c['resource'], $c['context']);
    $s = $shadowFn($c['subject'], $c['action'], $c['resource'], $c['context']);
    $rec = [
        'ts' => date('c'),
        'input' => $c,
        'active' => $a,
        'shadow' => $s,
        'match' => ($a['allowed'] === $s['allowed'] && $a['ruleId'] === $s['ruleId']),
    ];
    fwrite($fh, json_encode($rec, JSON_UNESCAPED_SLASHES) . "\n");
}
fclose($fh);
echo basename($outFile), " written\n";
