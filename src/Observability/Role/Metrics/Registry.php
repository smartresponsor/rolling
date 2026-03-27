<?php

declare(strict_types=1);

namespace App\Observability\Role\Metrics;

/**
 *
 */

/**
 *
 */
final class Registry
{
    /** @var array */
    private array $counters = [];
    /** @var array */
    private array $histograms = [];

    /**
     * @param string $name
     * @param string $help
     * @param array $labelNames
     * @return \App\Observability\Role\Metrics\Counter
     */
    public function counter(string $name, string $help = '', array $labelNames = []): Counter
    {
        if (!isset($this->counters[$name])) {
            $this->counters[$name] = new Counter($name, $help, $labelNames);
        }
        return $this->counters[$name];
    }

    /**
     * @param string $name
     * @param string $help
     * @param array $buckets
     * @param array $labelNames
     * @return \App\Observability\Role\Metrics\Histogram
     */
    public function histogram(string $name, string $help = '', array $buckets = [], array $labelNames = []): Histogram
    {
        if (!isset($this->histograms[$name])) {
            $this->histograms[$name] = new Histogram($name, $help, $buckets ?: [0.001, 0.005, 0.01, 0.025, 0.05, 0.1, 0.25, 0.5, 1, 2, 5], $labelNames);
        }
        return $this->histograms[$name];
    }

    /** @return array<int,Counter|Histogram> */
    public function all(): array
    {
        return array_values(array_merge($this->counters, $this->histograms));
    }
}
