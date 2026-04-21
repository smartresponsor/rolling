<?php

declare(strict_types=1);

namespace App\Rolling\Service\Cache;

/**
 * In-memory subject epoch map for cache invalidation.
 */
final class SubjectEpochs
{
    /** @var array<string,int> */
    private array $epochs = [];

    public function epochFor(string $subjectId): int
    {
        return $this->epochs[$subjectId] ?? 0;
    }

    public function bump(string $subjectId): void
    {
        $this->epochs[$subjectId] = ($this->epochs[$subjectId] ?? 0) + 1;
    }

    public function bumpMany(string ...$subjectIds): void
    {
        foreach ($subjectIds as $subjectId) {
            $this->bump($subjectId);
        }
    }
}
