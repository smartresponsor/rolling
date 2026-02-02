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

$added = [];
$removed = [];
$changed = [];

foreach ($ib as $id => $rb) {
    if (!array_key_exists($id, $ia)) {
        $added[$id] = $rb;
    } else {
        $ra = $ia[$id];
        $diff = [];
        foreach (['effect', 'reason'] as $k) {
            $va = $ra[$k] ?? null;
            $vb = $rb[$k] ?? null;
            if ($va !== $vb) $diff[$k] = ['from' => $va, 'to' => $vb];
        }
        $wa = $ra['when'] ?? [];
        $wb = $rb['when'] ?? [];
        if (json_encode($wa) !== json_encode($wb)) $diff['when'] = ['from' => $wa, 'to' => $wb];
        if ($diff) $changed[$id] = $diff;
    }
}
foreach ($ia as $id => $ra) {
    if (!array_key_exists($id, $ib)) $removed[$id] = true;
}

$out = [
    'ts' => date('c'),
    'src' => $src,
    'dst' => $dst,
    'stats' => ['added' => count($added), 'removed' => count($removed), 'changed' => count($changed)],
    'added' => $added,
    'removed' => array_keys($removed),
    'changed' => $changed,
];

$file = __DIR__ . '/../../report/policy_diff_' . date('Ymd_His') . '.json';
file_put_contents($file, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo basename($file), " written\n";
