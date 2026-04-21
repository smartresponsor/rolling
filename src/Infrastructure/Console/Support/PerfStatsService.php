<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Support;

use App\Rolling\Infrastructure\Console\Contract\PerfStatsInterface;

final class PerfStatsService implements PerfStatsInterface
{
    public function summarize(array $payload): array
    {
        $n = max(0, (int) ($payload['n'] ?? 0));
        $durationMs = max(0.0, (float) ($payload['duration_ms'] ?? 0.0));
        $throughput = $durationMs > 0.0 ? round(($n / $durationMs) * 1000.0, 3) : 0.0;

        return [
            'n' => $n,
            'duration_ms' => round($durationMs, 3),
            'per_item_ms' => round((float) ($payload['per_item_ms'] ?? 0.0), 3),
            'throughput_per_sec' => $throughput,
            'peak_mb' => round((float) ($payload['peak_mb'] ?? 0.0), 2),
        ];
    }
}
