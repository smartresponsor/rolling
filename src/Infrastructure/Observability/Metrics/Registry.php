<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Observability\Metrics;

final class Registry
{
    private array $counters = [];
    private array $histograms = [];

    public function counter(string $nameEntity, string $help = '', array $labelNames = []): Counter
    {
        if (!isset($this->counters[$nameEntity])) {
            $this->counters[$nameEntity] = new Counter($nameEntity, $help, $labelNames);
        }

        return $this->counters[$nameEntity];
    }

    public function histogram(string $nameEntity, string $help = '', array $buckets = [], array $labelNames = []): Histogram
    {
        if (!isset($this->histograms[$nameEntity])) {
            $this->histograms[$nameEntity] = new Histogram($nameEntity, $help, $buckets ?: [0.001, 0.005, 0.01, 0.025, 0.05, 0.1, 0.25, 0.5, 1, 2, 5], $labelNames);
        }

        return $this->histograms[$nameEntity];
    }

    /** @return array<int,Counter|Histogram> */
    public function all(): array
    {
        return array_values(array_merge($this->counters, $this->histograms));
    }
}
