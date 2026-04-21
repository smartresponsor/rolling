<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Observability\Metrics;

final class Counter
{
    /** @var array */
    private array $labelNames;
    /** @var array */
    private array $values = [];

    /**
     * @param string $name
     * @param string $help
     * @param array  $labelNames
     */
    public function __construct(private readonly string $name, private readonly string $help = '', array $labelNames = [])
    {
        $this->labelNames = array_values($labelNames);
    }

    /**
     * @param float $delta
     * @param array $labels
     */
    public function inc(float $delta = 1.0, array $labels = []): void
    {
        $key = $this->keyFor($labels);
        $this->values[$key] = ($this->values[$key] ?? 0.0) + $delta;
    }

    /** @return array{names:array<int,string>, series:array<string,float>} */
    public function dump(): array
    {
        return ['names' => $this->labelNames, 'series' => $this->values];
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
     * @param array $labels
     *
     * @return string
     */
    private function keyFor(array $labels): string
    {
        $vals = [];
        foreach ($this->labelNames as $n) {
            $vals[] = (string) ($labels[$n] ?? '');
        }

        return implode("\x1f", $vals);
    }
}
