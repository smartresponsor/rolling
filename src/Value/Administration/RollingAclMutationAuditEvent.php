<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Metadata-only audit event for ACL administration mutations.
 */
final readonly class RollingAclMutationAuditEvent
{
    /** @param array<string, mixed> $safeContext */
    public function __construct(
        private string $mutationType,
        private string $status,
        private string $subjectIdentifier,
        private string $permissionOrRoleKey,
        private string $scopeKey,
        private string $requestedBySubject,
        private string $safeMessage,
        private array $safeContext = [],
    ) {
    }

    public static function fromResult(RollingAclMutationRequest $request, RollingAclMutationResult $result): self
    {
        return new self(
            $request->mutationType(),
            $result->status(),
            $request->subjectIdentifier(),
            $request->permissionOrRoleKey(),
            $request->scopeKey(),
            $request->requestedBySubject(),
            $result->safeMessage(),
            $result->safeContext(),
        );
    }

    public function mutationType(): string
    {
        return $this->mutationType;
    }

    public function status(): string
    {
        return $this->status;
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

    public function safeMessage(): string
    {
        return $this->safeMessage;
    }

    /** @return array<string, mixed> */
    public function safeContext(): array
    {
        return $this->safeContext;
    }
}
