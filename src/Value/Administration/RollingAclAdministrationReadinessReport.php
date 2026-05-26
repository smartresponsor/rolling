<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Safe readiness report for Rolling ACL administration surfaces.
 */
final readonly class RollingAclAdministrationReadinessReport
{
    /**
     * @param list<string>         $supportedMutationTypes
     * @param array<string, mixed> $manifestSummary
     * @param array<string, mixed> $executionSummary
     * @param array<string, mixed> $storageReadiness
     */
    public function __construct(
        private \DateTimeImmutable $generatedAt,
        private array $supportedMutationTypes,
        private array $manifestSummary,
        private array $executionSummary,
        private array $storageReadiness = [],
    ) {
    }

    /** @return list<string> */
    public function supportedMutationTypes(): array
    {
        return $this->supportedMutationTypes;
    }

    /** @return array<string, mixed> */
    public function manifestSummary(): array
    {
        return $this->manifestSummary;
    }

    /** @return array<string, mixed> */
    public function executionSummary(): array
    {
        return $this->executionSummary;
    }

    /** @return array<string, mixed> */
    public function storageReadiness(): array
    {
        return $this->storageReadiness;
    }

    /** @return array<string, mixed> */
    public function toSafeArray(): array
    {
        return [
            'generatedAt' => $this->generatedAt->format(\DateTimeInterface::ATOM),
            'supportedMutationTypes' => $this->supportedMutationTypes,
            'manifestSummary' => $this->manifestSummary,
            'executionSummary' => $this->executionSummary,
            'storageReadiness' => $this->storageReadiness,
        ];
    }
}
