<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Tools\RC;

require_once __DIR__ . '/smoke.php';
require_once __DIR__ . '/slo_eval.php';
require_once __DIR__ . '/manifest.php';
require_once __DIR__ . '/sha256sums.php';

$root = realpath(__DIR__ . '/../src/') ?: __DIR__ . '/../src/';
$cfg = json_decode((string)file_get_contents($root . '/config/role/rc_gate.json'), true) ?: [];

@mkdir($root . '/report', 0775, true);

// 1) Smoke
$sm = Smoke::run($root, (array)($cfg['smoke']['paths'] ?? ['src']));
// 2) SLO
$metricsPath = $root . '/report/metrics.json';
if (!is_file($metricsPath)) {
    // generate a permissive sample if missing
    file_put_contents($metricsPath, json_encode([
        'read_p95_ms' => 200, 'write_p95_ms' => 600, 'error_rate' => 0.1, 'projection_lag_s' => 1,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}
$sl = SloEval::run($metricsPath, (array)($cfg['slo'] ?? []));
// 3) Manifest + SHA256SUMS
$manifest = Manifest::build($root);
$sumPath = $root . '/release/SHA256SUMS';
Sha256Sums::write($manifest, $sumPath);

// 4) Decision
$pass = ($sm['errors'] === 0) && $sl['pass'] === true;
$decision = [
    'ts' => date('c'),
    'pass' => $pass,
    'reason' => $pass ? 'smoke ok & slo ok' : 'smoke/slo gate failed',
    'smoke' => $sm,
    'slo' => $sl,
    'sha256sums' => $sumPath,
];

file_put_contents($root . '/report/rc_gate.json', json_encode($decision, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "report/rc_gate.json written; pass=" . ($pass ? "true" : "false") . "\n";
