#!/usr/bin/env php
<?php
declare(strict_types=1);

use function App\Tools\Policy\load_pel;
use function App\Tools\Policy\index_rules;

require_once __DIR__ . '/pel_util.php';

$src = $argv[1] ?? __DIR__ . '/../../policy/policy_v1.pel.json';
$dst = $argv[2] ?? __DIR__ . '/../../policy/policy_v2_shadow.pel.json';

@mkdir(__DIR__ . '/../../report', 0775, true);

$a = load_pel($src);
$b = load_pel($dst);
$ia = index_rules($a);
$ib = index_rules($b);

$steps = [];

foreach ($ib as $id => $rb) {
    if (!array_key_exists($id, $ia)) {
        $steps[] = ['op' => 'addRule', 'id' => $id, 'rule' => $rb];
    } else {
        $ra = $ia[$id];
        $chg = [];
        foreach (['effect', 'reason'] as $k) {
            if (($ra[$k] ?? null) !== ($rb[$k] ?? null)) $chg[$k] = $rb[$k] ?? null;
        }
        if (json_encode($ra['when'] ?? []) !== json_encode($rb['when'] ?? [])) {
            $chg['when'] = $rb['when'] ?? [];
        }
        if ($chg) $steps[] = ['op' => 'updateRule', 'id' => $id, 'changes' => $chg];
    }
}
foreach ($ia as $id => $ra) {
    if (!array_key_exists($id, $ib)) {
        $steps[] = ['op' => 'removeRule', 'id' => $id];
    }
}

# Emit YAML (simple)
$yaml = "migration:\n";
for ($i = 0; $i < count($steps); $i++) {
    $s = $steps[$i];
    $yaml .= "- op: " . $s['op'] . "\n  id: " . $s['id'] . "\n";
    if (isset($s['rule'])) {
        $yaml .= "  rule:\n";
        foreach ($s['rule'] as $k => $v) {
            $yaml .= "    " . $k . ": " . json_encode($v, JSON_UNESCAPED_SLASHES) . "\n";
        }
    }
    if (isset($s['changes'])) {
        $yaml .= "  changes:\n";
        foreach ($s['changes'] as $k => $v) {
            $yaml .= "    " . $k . ": " . json_encode($v, JSON_UNESCAPED_SLASHES) . "\n";
        }
    }
}

$file = __DIR__ . '/../../report/policy_migration_' . date('Ymd_His') . '.yaml';
file_put_contents($file, $yaml);
echo basename($file), " written\n";
