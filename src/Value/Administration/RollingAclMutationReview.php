<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Safe review DTO for Administering ACL mutation screens.
 *
 * The review intentionally carries only metadata needed for operator approval
 * or dry-run visualization. Subject secrets, session payloads, and raw policy
 * internals are forbidden here.
 */
final readonly class RollingAclMutationReview
{
    /**
     * @param list<string>         $steps
     * @param list<string>         $warnings
     * @param list<string>         $violations
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
        private array $violations = [],
        private array $safeContext = [],
    ) {
    }

    public static function fromPlan(RollingAclMutationPlan $plan): self
    {
        return new self(
            $plan->mutationType(),
            $plan->subjectIdentifier(),
            $plan->permissionOrRoleKey(),
            $plan->scopeKey(),
            $plan->valid(),
            $plan->steps(),
            $plan->warnings(),
            $plan->valid() ? [] : $plan->warnings(),
            $plan->safeContext() + ['review_mode' => 'dry-run'],
        );
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

    /** @return list<string> */
    public function violations(): array
    {
        return $this->violations;
    }

    /** @return array<string, mixed> */
    public function safeContext(): array
    {
        return $this->safeContext;
    }

    /** @return array<string, mixed> */
    public function toSafeArray(): array
    {
        return [
            'mutation_type' => $this->mutationType,
            'subject_identifier' => $this->subjectIdentifier,
            'permission_or_role_key' => $this->permissionOrRoleKey,
            'scope_key' => $this->scopeKey,
            'valid' => $this->valid,
            'steps' => $this->steps,
            'warnings' => $this->warnings,
            'violations' => $this->violations,
            'safe_context' => $this->safeContext,
        ];
    }
}
