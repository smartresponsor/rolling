<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Metadata-only validation result for Administering-driven ACL mutation requests.
 */
final readonly class RollingAclMutationValidationResult
{
    /** @param list<string> $violations */
    public function __construct(
        private bool $valid,
        private array $violations = [],
    ) {
    }

    public static function ok(): self
    {
        return new self(true);
    }

    /** @param list<string> $violations */
    public static function invalid(array $violations): self
    {
        return new self(false, array_values($violations));
    }

    public function valid(): bool
    {
        return $this->valid;
    }

    /** @return list<string> */
    public function violations(): array
    {
        return $this->violations;
    }
}
