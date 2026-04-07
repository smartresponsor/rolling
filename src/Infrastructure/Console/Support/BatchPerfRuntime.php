<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Support;

use App\Legacy\Policy\Batch\CheckBatchProcessor;
use App\Policy\Obligation\Obligations;
use App\Policy\V2\DecisionWithObligations;
use App\PolicyInterface\PdpV2Interface;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;

final class BatchPerfRuntime
{
    /**
     * @return array<string, mixed>
     */
    public function run(int $n, int $sleepUs, int $chunkSize): array
    {
        $inner = new class($sleepUs) implements PdpV2Interface {
            public function __construct(private readonly int $us)
            {
            }

            public function check(SubjectId $s, PermissionKey $a, Scope $sc, array $ctx = []): DecisionWithObligations
            {
                if ($this->us > 0) {
                    usleep($this->us);
                }

                return DecisionWithObligations::allow('ok', Obligations::empty());
            }
        };

        $processor = new CheckBatchProcessor($inner);
        $requests = [];
        for ($i = 0; $i < $n; ++$i) {
            $requests[] = [
                'subjectId' => sprintf('u%d', $i),
                'action' => 'message.read',
                'scopeType' => 'global',
                'context' => ['i' => $i],
            ];
        }

        $start = hrtime(true);
        $results = 0;
        foreach ($processor->process($requests, ['chunkSize' => $chunkSize, 'maxItems' => $n]) as $row) {
            ++$results;
        }
        $end = hrtime(true);
        $durationMs = ($end - $start) / 1_000_000.0;

        return [
            'n' => $n,
            'sleep_us' => $sleepUs,
            'chunk_size' => $chunkSize,
            'results' => $results,
            'duration_ms' => round($durationMs, 3),
            'per_item_ms' => round($results > 0 ? $durationMs / $results : 0.0, 3),
            'peak_mb' => round(memory_get_peak_usage(true) / 1048576, 2),
        ];
    }
}
