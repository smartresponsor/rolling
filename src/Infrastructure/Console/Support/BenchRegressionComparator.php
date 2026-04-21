<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Support;

final class BenchRegressionComparator
{
    /**
     * @param array<string, mixed> $current
     * @param array<string, mixed> $baseline
     *
     * @return array<string, mixed>
     */
    public function compare(array $current, array $baseline, float $maxP95RegressionPct, float $maxP99RegressionPct, float $maxBatchPerItemRegressionPct): array
    {
        $currentSummary = $this->indexSummary($current);
        $baselineSummary = $this->indexSummary($baseline);
        $scenarioNames = array_values(array_unique(array_merge(array_keys($currentSummary), array_keys($baselineSummary))));

        $scenarios = [];
        $failureCount = 0;
        foreach ($scenarioNames as $name) {
            $curr = $currentSummary[$name] ?? [];
            $base = $baselineSummary[$name] ?? [];
            $checks = [];

            if ([] === $curr || [] === $base) {
                $checks[] = [
                    'name' => 'scenario_presence',
                    'baseline' => [] !== $base,
                    'current' => [] !== $curr,
                    'delta_pct' => null,
                    'operator' => 'match',
                    'budget_pct' => 0.0,
                    'ok' => false,
                ];
            } else {
                if (array_key_exists('p95_ms', $curr) || array_key_exists('p95_ms', $base)) {
                    $checks[] = $this->regressionCheck('p95_ms', $curr, $base, $maxP95RegressionPct);
                    $checks[] = $this->regressionCheck('p99_ms', $curr, $base, $maxP99RegressionPct);
                }
                if (array_key_exists('per_item_ms', $curr) || array_key_exists('per_item_ms', $base)) {
                    $checks[] = $this->regressionCheck('per_item_ms', $curr, $base, $maxBatchPerItemRegressionPct);
                }
            }

            foreach ($checks as $check) {
                if (($check['ok'] ?? false) !== true) {
                    ++$failureCount;
                }
            }

            $scenarios[] = [
                'name' => $name,
                'checks' => $checks,
            ];
        }

        return [
            'ok' => 0 === $failureCount,
            'baseline_kind' => (string) ($baseline['kind'] ?? 'unknown'),
            'current_kind' => (string) ($current['kind'] ?? 'unknown'),
            'scenario_count' => count($scenarios),
            'failure_count' => $failureCount,
            'scenarios' => $scenarios,
        ];
    }

    /**
     * @param array<string, mixed> $report
     *
     * @return array<string, array<string, mixed>>
     */
    private function indexSummary(array $report): array
    {
        $summary = [];
        $stats = is_array($report['stats'] ?? null) ? $report['stats'] : [];
        foreach (($stats['summary'] ?? []) as $scenario) {
            if (!is_array($scenario)) {
                continue;
            }
            $summary[(string) ($scenario['name'] ?? 'unknown')] = $scenario;
        }

        return $summary;
    }

    /**
     * @param array<string, mixed> $current
     * @param array<string, mixed> $baseline
     *
     * @return array<string, mixed>
     */
    private function regressionCheck(string $metric, array $current, array $baseline, float $budgetPct): array
    {
        $curr = (float) ($current[$metric] ?? 0.0);
        $base = (float) ($baseline[$metric] ?? 0.0);
        $deltaPct = $base > 0.0 ? (($curr - $base) / $base) * 100.0 : 0.0;

        return [
            'name' => $metric,
            'baseline' => round($base, 4),
            'current' => round($curr, 4),
            'delta_pct' => round($deltaPct, 4),
            'operator' => '<=',
            'budget_pct' => round($budgetPct, 4),
            'ok' => $deltaPct <= $budgetPct,
        ];
    }
}
