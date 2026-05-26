<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Safe filter for ACL mutation execution event reports.
 */
final readonly class RollingAclMutationExecutionFilter
{
    public function __construct(
        private ?string $mutationType = null,
        private ?string $status = null,
        private ?string $subjectIdentifier = null,
        private int $limit = 100,
    ) {
    }

    public static function recent(int $limit = 100): self
    {
        return new self(limit: $limit);
    }

    public function mutationType(): ?string
    {
        return $this->mutationType;
    }

    public function status(): ?string
    {
        return $this->status;
    }

    public function subjectIdentifier(): ?string
    {
        return $this->subjectIdentifier;
    }

    public function limit(): int
    {
        return max(1, min(500, $this->limit));
    }

    /** @return array<string, mixed> */
    public function toSafeArray(): array
    {
        return [
            'mutation_type' => $this->mutationType,
            'status' => $this->status,
            'subject_identifier' => $this->subjectIdentifier,
            'limit' => $this->limit(),
        ];
    }
}
