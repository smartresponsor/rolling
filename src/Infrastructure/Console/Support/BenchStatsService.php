<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Support;

use App\Rolling\Infrastructure\Console\Contract\PerfStatsInterface;

final class BenchStatsService implements PerfStatsInterface
{
    public function summarize(array $payload): array
    {
        $summary = [];
        foreach (($payload['scenarios'] ?? []) as $scenario) {
            if (!is_array($scenario)) {
                continue;
            }

            $name = (string) ($scenario['name'] ?? 'unknown');
            if (isset($scenario['samples_ms']) && is_array($scenario['samples_ms'])) {
                $summary[] = [
                    'name' => $name,
                    'n' => (int) ($scenario['n'] ?? count($scenario['samples_ms'])),
                    'p50_ms' => $this->percentile($scenario['samples_ms'], 0.50),
                    'p95_ms' => $this->percentile($scenario['samples_ms'], 0.95),
                    'p99_ms' => $this->percentile($scenario['samples_ms'], 0.99),
                ];
                continue;
            }

            $summary[] = [
                'name' => $name,
                'n' => (int) ($scenario['n'] ?? 0),
                'duration_ms' => round((float) ($scenario['duration_ms'] ?? 0.0), 3),
                'per_item_ms' => round((float) ($scenario['per_item_ms'] ?? 0.0), 3),
            ];
        }

        return [
            'scenario_count' => count($summary),
            'summary' => $summary,
        ];
    }

    /**
     * @param array<int, float|int> $samples
     */
    private function percentile(array $samples, float $p): float
    {
        if ([] === $samples) {
            return 0.0;
        }

        sort($samples);
        $idx = (int) max(0, min(count($samples) - 1, floor($p * (count($samples) - 1))));

        return round((float) $samples[$idx], 4);
    }
}
