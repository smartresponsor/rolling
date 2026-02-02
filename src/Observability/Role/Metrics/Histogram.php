<?php
declare(strict_types=1);

namespace App\Observability\Role\Metrics;

/**
 *
 */

/**
 *
 */
final class Histogram
{
    /** @var array<float,int> */
    private array $buckets;
    /** @var array */
    private array $labelNames;
    /** @var array */
    private array $values = [];

    /**
     * @param string $name
     * @param string $help
     * @param array $buckets
     * @param array $labelNames
     */
    public function __construct(private readonly string $name, private readonly string $help = '', array $buckets = [], array $labelNames = [])
    {
        $this->buckets = array_values($buckets);
        sort($this->buckets, SORT_NUMERIC);
        if (!$this->buckets || end($this->buckets) !== INF) {
            $this->buckets[] = INF;
        }
        $this->labelNames = array_values($labelNames);
    }

    /**
     * @param float $value
     * @param array $labels
     */
    public function observe(float $value, array $labels = []): void
    {
        $k = $this->keyFor($labels);
        if (!isset($this->values[$k])) {
            $this->values[$k] = ['buckets' => array_fill_keys($this->buckets, 0), 'sum' => 0.0, 'count' => 0];
        }
        foreach (array_keys($this->values[$k]['buckets']) as $b) {
            if ($value <= (float)$b) {
                $this->values[$k]['buckets'][$b]++;
            }
        }
        $this->values[$k]['sum'] += $value;
        $this->values[$k]['count']++;
    }

    /** @return array{names:array<int,string>, data:array<string,array{buckets:array<float,int>,sum:float,count:int}>} */
    public function dump(): array
    {
        return ['names' => $this->labelNames, 'data' => $this->values];
    }

    /**
     * @return string
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function help(): string
    {
        return $this->help;
    }

    /**
     * @return array
     */
    public function buckets(): array
    {
        return $this->buckets;
    }

    /**
     * @param array $labels
     * @return string
     */
    private function keyFor(array $labels): string
    {
        $vals = [];
        foreach ($this->labelNames as $n) {
            $vals[] = (string)($labels[$n] ?? '');
        }
        return implode("\x1f", $vals);
    }
}
