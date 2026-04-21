<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Contract;

interface PerfStatsInterface
{
    /**
     * @param array<string, mixed> $payload
     *
     * @return array<string, mixed>
     */
    public function summarize(array $payload): array;
}
