<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Safe request DTO for Administering-driven ACL mutations.
 */
final readonly class RollingAclMutationRequest
{
    /** @param array<string, mixed> $safeContext */
    public function __construct(
        private string $mutationType,
        private string $subjectIdentifier,
        private string $permissionOrRoleKey,
        private string $scopeKey = 'global',
        private string $requestedBySubject = 'system',
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

    public function requestedBySubject(): string
    {
        return $this->requestedBySubject;
    }

    /** @return array<string, mixed> */
    public function safeContext(): array
    {
        return $this->safeContext;
    }
}
