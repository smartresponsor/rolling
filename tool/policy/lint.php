#!/usr/bin/env php
<?php
declare(strict_types=1);

use function App\Tools\Policy\load_pel;
use function App\Tools\Policy\is_valid_when_expr;

require_once __DIR__ . '/pel_util.php';

$path = $argv[1] ?? __DIR__ . '/../../policy/policy_v1.pel.json';
$spec = load_pel($path);
@mkdir(__DIR__ . '/../../report', 0775, true);

$errors = [];
$warnings = [];
$seen = [];

$rules = $spec['rules'] ?? [];
if (!is_array($rules)) $rules = [];

foreach ($rules as $i => $r) {
    $id = (string)($r['id'] ?? '');
    if ($id === '') {
        $errors[] = ['idx' => $i, 'msg' => 'missing id'];
        continue;
    }
    if (!preg_match('/^[a-z0-9_.\-]+$/', $id)) {
        $errors[] = ['idx' => $i, 'id' => $id, 'msg' => 'invalid id chars'];
    }
    if (isset($seen[$id])) $errors[] = ['idx' => $i, 'id' => $id, 'msg' => 'duplicate id'];
    $seen[$id] = true;

    $effect = (string)($r['effect'] ?? '');
    if ($effect !== 'allow' && $effect !== 'deny') $errors[] = ['id' => $id, 'msg' => 'invalid effect (must be allow|deny)'];

    $reason = (string)($r['reason'] ?? '');
    if ($reason === '') $warnings[] = ['id' => $id, 'msg' => 'empty reason'];

    $when = $r['when'] ?? [];
    if ($when !== null && !is_array($when)) {
        $errors[] = ['id' => $id, 'msg' => 'when must be array'];
    } else {
        foreach ((array)$when as $expr) {
            if (!is_valid_when_expr((string)$expr)) {
                $errors[] = ['id' => $id, 'expr' => $expr, 'msg' => 'invalid when expression'];
            }
        }
    }
}

$out = [
    'ts' => date('c'),
    'path' => $path,
    'errors' => $errors,
    'warnings' => $warnings,
    'error_count' => count($errors),
    'warning_count' => count($warnings),
];
$file = __DIR__ . '/../../report/policy_lint_' . date('Ymd_His') . '.json';
file_put_contents($file, json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo basename($file), " written\n";
if (count($errors) > 0) exit(1);
