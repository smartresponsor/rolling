<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Support;

final class PerfRegressionComparator
{
    /**
     * @param array<string, mixed> $current
     * @param array<string, mixed> $baseline
     *
     * @return array<string, mixed>
     */
    public function compare(array $current, array $baseline, float $maxDurationRegressionPct, float $maxPerItemRegressionPct, float $maxPeakRegressionPct, float $maxThroughputDropPct): array
    {
        $currentStats = is_array($current['stats'] ?? null) ? $current['stats'] : [];
        $baselineStats = is_array($baseline['stats'] ?? null) ? $baseline['stats'] : [];

        $checks = [
            $this->regressionCheck('duration_ms', $currentStats, $baselineStats, '<=', $maxDurationRegressionPct),
            $this->regressionCheck('per_item_ms', $currentStats, $baselineStats, '<=', $maxPerItemRegressionPct),
            $this->regressionCheck('peak_mb', $currentStats, $baselineStats, '<=', $maxPeakRegressionPct),
            $this->regressionCheck('throughput_per_sec', $currentStats, $baselineStats, '>=', $maxThroughputDropPct),
        ];

        $failures = array_values(array_filter($checks, static fn (array $check): bool => true !== $check['ok']));

        return [
            'ok' => [] === $failures,
            'baseline_kind' => (string) ($baseline['kind'] ?? 'unknown'),
            'current_kind' => (string) ($current['kind'] ?? 'unknown'),
            'checks' => $checks,
            'failure_count' => count($failures),
        ];
    }

    /**
     * @param array<string, mixed> $currentStats
     * @param array<string, mixed> $baselineStats
     *
     * @return array<string, mixed>
     */
    private function regressionCheck(string $metric, array $currentStats, array $baselineStats, string $direction, float $budgetPct): array
    {
        $current = (float) ($currentStats[$metric] ?? 0.0);
        $baseline = (float) ($baselineStats[$metric] ?? 0.0);
        $deltaPct = $baseline > 0.0 ? (($current - $baseline) / $baseline) * 100.0 : 0.0;

        $ok = match ($direction) {
            '<=' => $deltaPct <= $budgetPct,
            '>=' => (-1.0 * $deltaPct) <= $budgetPct,
            default => false,
        };

        return [
            'name' => $metric,
            'baseline' => round($baseline, 4),
            'current' => round($current, 4),
            'delta_pct' => round($deltaPct, 4),
            'operator' => $direction,
            'budget_pct' => round($budgetPct, 4),
            'ok' => $ok,
        ];
    }
}
