<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Support;

final class PerfThresholdEvaluator
{
    /**
     * @param array<string, mixed> $report
     *
     * @return array<string, mixed>
     */
    public function evaluate(array $report, float $maxDurationMs, float $maxPerItemMs, float $minThroughputPerSec, float $maxPeakMb): array
    {
        $stats = is_array($report['stats'] ?? null) ? $report['stats'] : [];
        $checks = [
            [
                'name' => 'max_duration_ms',
                'actual' => round((float) ($stats['duration_ms'] ?? 0.0), 3),
                'operator' => '<=',
                'expected' => round($maxDurationMs, 3),
            ],
            [
                'name' => 'max_per_item_ms',
                'actual' => round((float) ($stats['per_item_ms'] ?? 0.0), 3),
                'operator' => '<=',
                'expected' => round($maxPerItemMs, 3),
            ],
            [
                'name' => 'min_throughput_per_sec',
                'actual' => round((float) ($stats['throughput_per_sec'] ?? 0.0), 3),
                'operator' => '>=',
                'expected' => round($minThroughputPerSec, 3),
            ],
            [
                'name' => 'max_peak_mb',
                'actual' => round((float) ($stats['peak_mb'] ?? 0.0), 3),
                'operator' => '<=',
                'expected' => round($maxPeakMb, 3),
            ],
        ];

        foreach ($checks as $idx => $check) {
            $checks[$idx]['ok'] = match ($check['operator']) {
                '<=' => $check['actual'] <= $check['expected'],
                '>=' => $check['actual'] >= $check['expected'],
                default => false,
            };
        }

        $failures = array_values(array_filter($checks, static fn (array $check): bool => true !== $check['ok']));

        return [
            'ok' => [] === $failures,
            'checks' => $checks,
            'failure_count' => count($failures),
        ];
    }
}
