<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Tools\RC;

/**
 *
 */

/**
 *
 */
final class SloEval
{
    /** @return array{pass:bool, metrics:array<string,float>, gate:array<string,float>} */
    public static function run(string $metricsPath, array $gate): array
    {
        $j = @json_decode((string)@file_get_contents($metricsPath), true);
        if (!is_array($j)) $j = [];
        $m = [
            'read_p95_ms' => (float)($j['read_p95_ms'] ?? 99999),
            'write_p95_ms' => (float)($j['write_p95_ms'] ?? 99999),
            'error_rate' => (float)($j['error_rate'] ?? 100.0),
            'projection_lag_s' => (float)($j['projection_lag_s'] ?? 99999),
        ];
        $pass = $m['read_p95_ms'] <= ($gate['read_p95_ms_max'] ?? 250)
            && $m['write_p95_ms'] <= ($gate['write_p95_ms_max'] ?? 700)
            && $m['error_rate'] <= ($gate['error_rate_max'] ?? 0.5)
            && $m['projection_lag_s'] <= ($gate['projection_lag_s_max'] ?? 5);
        return ['pass' => $pass, 'metrics' => $m, 'gate' => $gate];
    }
}
