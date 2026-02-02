<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

$root = realpath(__DIR__ . '/../src/') ?: __DIR__ . '/../src/';
@mkdir($root . '/report', 0775, true);
file_put_contents($root . '/report/metrics.json', json_encode([
    'read_p95_ms' => 210,
    'write_p95_ms' => 650,
    'error_rate' => 0.2,
    'projection_lag_s' => 2,
], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "report/metrics.json written\n";
