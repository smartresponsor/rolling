<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Observability\Metrics;

final class Counter
{
    private array $labelNames;
    private array $values = [];

    public function __construct(private readonly string $nameEntity, private readonly string $help = '', array $labelNames = [])
    {
        $this->labelNames = array_values($labelNames);
    }

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

    public function nameEntity(): string
    {
        return $this->nameEntity;
    }

    public function help(): string
    {
        return $this->help;
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
