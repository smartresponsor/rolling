<?php

declare(strict_types=1);

namespace App\Rolling\Event;

use App\Rolling\Value\Administration\RollingAclMutationRequest;
use App\Rolling\Value\Administration\RollingAclMutationResult;

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

    /** Creates the value from the supplied domain inputs. */
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

    /** Executes the mutationType operation. */
    public function mutationType(): string
    {
        return $this->mutationType;
    }

    /** Executes the status operation. */
    public function status(): string
    {
        return $this->status;
    }

    /** Executes the subjectIdentifier operation. */
    public function subjectIdentifier(): string
    {
        return $this->subjectIdentifier;
    }

    /** Executes the permissionOrRoleKey operation. */
    public function permissionOrRoleKey(): string
    {
        return $this->permissionOrRoleKey;
    }

    /** Executes the scopeKey operation. */
    public function scopeKey(): string
    {
        return $this->scopeKey;
    }

    /** Executes the requestedBySubject operation. */
    public function requestedBySubject(): string
    {
        return $this->requestedBySubject;
    }

    /** Executes the safeMessage operation. */
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
