<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Support;

final class BenchThresholdEvaluator
{
    /**
     * @param array<string, mixed> $report
     * @return array<string, mixed>
     */
    public function evaluate(array $report, float $maxP95Ms, float $maxP99Ms, float $maxBatchPerItemMs): array
    {
        $summary = [];
        $stats = is_array($report['stats'] ?? null) ? $report['stats'] : [];
        foreach (($stats['summary'] ?? []) as $scenario) {
            if (!is_array($scenario)) {
                continue;
            }

            $name = (string) ($scenario['name'] ?? 'unknown');
            if (isset($scenario['p95_ms'])) {
                $summary[] = [
                    'name' => $name,
                    'checks' => [
                        [
                            'name' => 'max_p95_ms',
                            'actual' => round((float) ($scenario['p95_ms'] ?? 0.0), 4),
                            'operator' => '<=',
                            'expected' => round($maxP95Ms, 4),
                            'ok' => (float) ($scenario['p95_ms'] ?? 0.0) <= $maxP95Ms,
                        ],
                        [
                            'name' => 'max_p99_ms',
                            'actual' => round((float) ($scenario['p99_ms'] ?? 0.0), 4),
                            'operator' => '<=',
                            'expected' => round($maxP99Ms, 4),
                            'ok' => (float) ($scenario['p99_ms'] ?? 0.0) <= $maxP99Ms,
                        ],
                    ],
                ];
                continue;
            }

            $summary[] = [
                'name' => $name,
                'checks' => [
                    [
                        'name' => 'max_batch_per_item_ms',
                        'actual' => round((float) ($scenario['per_item_ms'] ?? 0.0), 4),
                        'operator' => '<=',
                        'expected' => round($maxBatchPerItemMs, 4),
                        'ok' => (float) ($scenario['per_item_ms'] ?? 0.0) <= $maxBatchPerItemMs,
                    ],
                ],
            ];
        }

        $failureCount = 0;
        foreach ($summary as $scenarioSummary) {
            foreach ($scenarioSummary['checks'] as $check) {
                if (($check['ok'] ?? false) !== true) {
                    ++$failureCount;
                }
            }
        }

        return [
            'ok' => $failureCount === 0,
            'scenario_count' => count($summary),
            'failure_count' => $failureCount,
            'scenarios' => $summary,
        ];
    }
}
