<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Support;

final class PerfStatsReport
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
            'kind' => 'perf',
            'generated_at' => gmdate(DATE_ATOM),
            'n' => (int) ($payload['n'] ?? 0),
            'sleep_us' => (int) ($payload['sleep_us'] ?? 0),
            'chunk' => (int) ($payload['chunk_size'] ?? 0),
            'input' => [
                'n' => (int) ($payload['n'] ?? 0),
                'sleep_us' => (int) ($payload['sleep_us'] ?? 0),
                'chunk_size' => (int) ($payload['chunk_size'] ?? 0),
            ],
            'stats' => $stats,
        ];

        if ($detailed) {
            $report['details'] = [
                'results' => (int) ($payload['results'] ?? 0),
                'duration_ms' => (float) ($payload['duration_ms'] ?? 0.0),
                'per_item_ms' => (float) ($payload['per_item_ms'] ?? 0.0),
                'peak_mb' => (float) ($payload['peak_mb'] ?? 0.0),
            ];
        }

        if ($trace) {
            $report['trace'] = [
                'throughput_per_chunk' => $this->throughputPerChunk($payload),
                'memory_profile' => [
                    'peak_mb' => round((float) ($payload['peak_mb'] ?? 0.0), 2),
                    'estimated_chunk_density' => max(1, (int) ($payload['chunk_size'] ?? 1)),
                ],
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

    /**
     * @param array<string, mixed> $payload
     *
     * @return array<int, array<string, float|int>>
     */
    private function throughputPerChunk(array $payload): array
    {
        $n = max(1, (int) ($payload['n'] ?? 0));
        $chunk = max(1, (int) ($payload['chunk_size'] ?? 1));
        $durationMs = max(0.001, (float) ($payload['duration_ms'] ?? 0.001));
        $chunks = (int) ceil($n / $chunk);
        $perChunkMs = $durationMs / max(1, $chunks);
        $result = [];
        for ($i = 1; $i <= min($chunks, 12); ++$i) {
            $result[] = [
                'chunk' => $i,
                'items' => min($chunk, max(0, $n - (($i - 1) * $chunk))),
                'duration_ms' => round($perChunkMs, 3),
                'throughput_per_sec' => round(($chunk / $perChunkMs) * 1000.0, 3),
            ];
        }

        return $result;
    }
}
