<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Support;

final class BenchStatsReport
{
    /**
     * @param array<string, mixed> $payload
     * @param array<string, mixed> $stats
     *
     * @return array<string, mixed>
     */
    public function build(array $payload, array $stats, bool $detailed = false, bool $trace = false): array
    {
        $report = [
            'ok' => true,
            'kind' => 'bench',
            'generated_at' => gmdate(DATE_ATOM),
            'input' => [
                'iterations' => (int) ($payload['iterations'] ?? 0),
                'batch_n' => (int) ($payload['batch_n'] ?? 0),
                'rpc_us' => (int) ($payload['rpc_us'] ?? 0),
            ],
            'stats' => $stats,
        ];

        if ($detailed) {
            $report['details'] = [
                'scenarios' => $payload['scenarios'] ?? [],
            ];
        }

        if ($trace) {
            $report['trace'] = [
                'scenario_names' => array_values(array_map(
                    static fn (array $scenario): string => (string) ($scenario['name'] ?? 'unknown'),
                    array_values(array_filter($payload['scenarios'] ?? [], 'is_array')),
                )),
                'scenario_count' => (int) ($stats['scenario_count'] ?? 0),
            ];
        }

        return $report;
    }

    /**
     * @param array<string, mixed> $report
     */
    public function persist(array $report, string $outputPath): string
    {
        $directory = \dirname($outputPath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        file_put_contents($outputPath, json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $outputPath;
    }
}
