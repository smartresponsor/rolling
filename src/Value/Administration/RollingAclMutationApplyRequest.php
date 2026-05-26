<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Safe request DTO for promoting a reviewed ACL mutation toward application.
 *
 * This DTO is intentionally metadata-only and does not execute the mutation by
 * itself. Application remains owned by Rolling ACL administration services.
 */
final readonly class RollingAclMutationApplyRequest
{
    /** @param array<string, mixed> $safeContext */
    public function __construct(
        private string $requestKey,
        private string $mutationType,
        private string $subjectIdentifier,
        private string $permissionOrRoleKey,
        private string $scopeKey,
        private string $requestedBySubject,
        private bool $reviewValid,
        private array $safeContext = [],
    ) {
    }

    public static function fromReview(
        string $requestKey,
        RollingAclMutationReview $review,
        string $requestedBySubject,
        array $safeContext = [],
    ): self {
        return new self(
            $requestKey,
            $review->mutationType(),
            $review->subjectIdentifier(),
            $review->permissionOrRoleKey(),
            $review->scopeKey(),
            $requestedBySubject,
            $review->valid(),
            $safeContext + ['source' => 'rolling_acl_review', 'review_valid' => $review->valid()],
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

    public function reviewValid(): bool
    {
        return $this->reviewValid;
    }

    /** @return array<string, mixed> */
    public function safeContext(): array
    {
        return $this->safeContext;
    }

    public function toMutationRequest(): RollingAclMutationRequest
    {
        return new RollingAclMutationRequest(
            $this->mutationType,
            $this->subjectIdentifier,
            $this->permissionOrRoleKey,
            $this->scopeKey,
            $this->requestedBySubject,
            $this->safeContext,
        );
    }
}
