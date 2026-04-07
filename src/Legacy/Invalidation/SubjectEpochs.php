<?php
declare(strict_types=1);

namespace App\Legacy\Invalidation;

/** Простейшая карта subjectId → epoch (int). */
final class SubjectEpochs
{
    /** @var array */
    private array $epochs = [];

    /**
     * @param string $subjectId
     * @return int
     */
    public function epochFor(string $subjectId): int
    {
        return $this->epochs[$subjectId] ?? 0;
    }

    /**
     * @param string $subjectId
     * @return void
     */
    public function bump(string $subjectId): void
    {
        $this->epochs[$subjectId] = ($this->epochs[$subjectId] ?? 0) + 1;
    }

    /** Массовое повышение (например, при глобальном ревоке). */
    public function bumpMany(string ...$subjectIds): void
    {
        foreach ($subjectIds as $s) {
            $this->bump($s);
        }
    }
}
