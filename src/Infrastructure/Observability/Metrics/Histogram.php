<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Observability\Metrics;

final class Histogram
{
    /** @var array<float,int> */
    private array $buckets;
    private array $labelNames;
    private array $values = [];

    public function __construct(private readonly string $nameEntity, private readonly string $help = '', array $buckets = [], array $labelNames = [])
    {
        $this->buckets = array_values($buckets);
        sort($this->buckets, SORT_NUMERIC);
        if (!$this->buckets || INF !== end($this->buckets)) {
            $this->buckets[] = INF;
        }
        $this->labelNames = array_values($labelNames);
    }

    public function observe(float $value, array $labels = []): void
    {
        $k = $this->keyFor($labels);
        if (!isset($this->values[$k])) {
            $this->values[$k] = ['buckets' => array_fill_keys($this->buckets, 0), 'sum' => 0.0, 'count' => 0];
        }
        foreach (array_keys($this->values[$k]['buckets']) as $b) {
            if ($value <= (float) $b) {
                ++$this->values[$k]['buckets'][$b];
            }
        }
        $this->values[$k]['sum'] += $value;
        ++$this->values[$k]['count'];
    }

    /** @return array{names:array<int,string>, data:array<string,array{buckets:array<float,int>,sum:float,count:int}>} */
    public function dump(): array
    {
        return ['names' => $this->labelNames, 'data' => $this->values];
    }

    public function nameEntity(): string
    {
        return $this->nameEntity;
    }

    public function help(): string
    {
        return $this->help;
    }

    public function buckets(): array
    {
        return $this->buckets;
    }

    private function keyFor(array $labels): string
    {
        $vals = [];
        foreach ($this->labelNames as $n) {
            $vals[] = (string) ($labels[$n] ?? '');
        }

        return implode("\x1f", $vals);
    }
}
