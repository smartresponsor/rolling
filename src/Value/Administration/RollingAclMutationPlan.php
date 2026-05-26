<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Dry-run plan for an Administering-driven ACL mutation.
 *
 * The plan exposes only safe metadata for review screens and audit trails.
 */
final readonly class RollingAclMutationPlan
{
    /**
     * @param list<string>         $steps
     * @param list<string>         $warnings
     * @param array<string, mixed> $safeContext
     */
    public function __construct(
        private string $mutationType,
        private string $subjectIdentifier,
        private string $permissionOrRoleKey,
        private string $scopeKey,
        private bool $valid,
        private array $steps = [],
        private array $warnings = [],
        private array $safeContext = [],
    ) {
    }

    public function mutationType(): string
    {
        return $this->mutationType;
    }

    public function subjectIdentifier(): string
    {
        return $this->subjectIdentifier;
    }

    public function permissionOrRoleKey(): string
    {
        return $this->permissionOrRoleKey;
    }

    public function scopeKey(): string
    {
        return $this->scopeKey;
    }

    public function valid(): bool
    {
        return $this->valid;
    }

    /** @return list<string> */
    public function steps(): array
    {
        return $this->steps;
    }

    /** @return list<string> */
    public function warnings(): array
    {
        return $this->warnings;
    }

    /** @return array<string, mixed> */
    public function safeContext(): array
    {
        return $this->safeContext;
    }
}
