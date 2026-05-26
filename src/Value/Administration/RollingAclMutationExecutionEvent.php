<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Safe execution event emitted by the Rolling ACL execution gateway.
 *
 * This event is intentionally metadata-only. It must not contain sessions,
 * passwords, secrets, raw policy internals, or decrypted configuration values.
 */
final readonly class RollingAclMutationExecutionEvent
{
    /** @param array<string, mixed> $safeContext */
    public function __construct(
        private string $requestKey,
        private string $mutationType,
        private string $subjectIdentifier,
        private string $permissionOrRoleKey,
        private string $scopeKey,
        private string $requestedBySubject,
        private string $status,
        private bool $succeeded,
        private string $safeMessage,
        private array $safeContext = [],
        private ?\DateTimeImmutable $createdAt = null,
    ) {
    }

    public static function fromApplyRequest(RollingAclMutationApplyRequest $request, RollingAclMutationResult $result): self
    {
        return new self(
            $request->requestKey(),
            $request->mutationType(),
            $request->subjectIdentifier(),
            $request->permissionOrRoleKey(),
            $request->scopeKey(),
            $request->requestedBySubject(),
            $result->status(),
            $result->succeeded(),
            $result->safeMessage(),
            $result->safeContext() + ['review_valid' => $request->reviewValid()],
        );
    }

    public function requestKey(): string
    {
        return $this->requestKey;
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

    public function status(): string
    {
        return $this->status;
    }

    public function succeeded(): bool
    {
        return $this->succeeded;
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

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt ?? new \DateTimeImmutable();
    }

    /** @return array<string, mixed> */
    public function toSafeArray(): array
    {
        return [
            'request_key' => $this->requestKey,
            'mutation_type' => $this->mutationType,
            'subject_identifier' => $this->subjectIdentifier,
            'permission_or_role_key' => $this->permissionOrRoleKey,
            'scope_key' => $this->scopeKey,
            'requested_by_subject' => $this->requestedBySubject,
            'status' => $this->status,
            'succeeded' => $this->succeeded,
            'safe_message' => $this->safeMessage,
            'safe_context' => $this->safeContext,
            'created_at' => $this->createdAt()->format(DATE_ATOM),
        ];
    }
}
