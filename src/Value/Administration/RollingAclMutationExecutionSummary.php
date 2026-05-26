<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Metadata-only summary of Rolling ACL mutation execution events.
 */
final readonly class RollingAclMutationExecutionSummary
{
    /**
     * @param array<string, int> $countByStatus
     * @param array<string, int> $countByMutationType
     */
    public function __construct(
        private int $total,
        private array $countByStatus = [],
        private array $countByMutationType = [],
        private ?\DateTimeImmutable $latestAt = null,
    ) {
    }

    public function total(): int
    {
        return $this->total;
    }

    /** @return array<string, int> */
    public function countByStatus(): array
    {
        return $this->countByStatus;
    }

    /** @return array<string, int> */
    public function countByMutationType(): array
    {
        return $this->countByMutationType;
    }

    public function latestAt(): ?\DateTimeImmutable
    {
        return $this->latestAt;
    }

    /** @return array<string, mixed> */
    public function toSafeArray(): array
    {
        return [
            'total' => $this->total,
            'count_by_status' => $this->countByStatus,
            'count_by_mutation_type' => $this->countByMutationType,
            'latest_at' => $this->latestAt?->format(DATE_ATOM),
        ];
    }
}
