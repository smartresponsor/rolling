<?php

declare(strict_types=1);

namespace App\Rolling\Entity\Acl;

use App\Rolling\Value\Administration\RollingAclMutationExecutionEvent;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Persisted metadata-only ACL mutation execution event.
 *
 * The entity intentionally stores only safe execution metadata. It must never
 * include passwords, sessions, secrets, decrypted configuration, or raw policy
 * internals.
 */
#[ORM\Entity]
#[ORM\Table(name: 'rolling_acl_mutation_execution_event')]
#[ORM\Index(name: 'idx_rolling_acl_execution_request_key', columns: ['request_key'])]
#[ORM\Index(name: 'idx_rolling_acl_execution_mutation_type', columns: ['mutation_type'])]
#[ORM\Index(name: 'idx_rolling_acl_execution_subject', columns: ['subject_identifier'])]
#[ORM\Index(name: 'idx_rolling_acl_execution_status', columns: ['status'])]
#[ORM\Index(name: 'idx_rolling_acl_execution_created_at', columns: ['created_at'])]
class RollingAclMutationExecutionEventEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'request_key', type: 'string', length: 180)]
    private string $requestKey;

    #[ORM\Column(name: 'mutation_type', type: 'string', length: 80)]
    private string $mutationType;

    #[ORM\Column(name: 'subject_identifier', type: 'string', length: 220)]
    private string $subjectIdentifier;

    #[ORM\Column(name: 'permission_or_role_key', type: 'string', length: 180)]
    private string $permissionOrRoleKey;

    #[ORM\Column(name: 'scope_key', type: 'string', length: 220)]
    private string $scopeKey;

    #[ORM\Column(name: 'requested_by_subject', type: 'string', length: 220)]
    private string $requestedBySubject;

    #[ORM\Column(name: 'status', type: 'string', length: 40)]
    private string $status;

    #[ORM\Column(name: 'succeeded', type: 'boolean')]
    private bool $succeeded;

    #[ORM\Column(name: 'safe_message', type: 'text')]
    private string $safeMessage;

    /** @var array<string, mixed> */
    #[ORM\Column(name: 'safe_context', type: Types::JSON)]
    private array $safeContext = [];

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $createdAt;

    /** @param array<string, mixed> $safeContext */
    public function __construct(
        string $requestKey = '',
        string $mutationType = '',
        string $subjectIdentifier = '',
        string $permissionOrRoleKey = '',
        string $scopeKey = 'global',
        string $requestedBySubject = '',
        string $status = 'unknown',
        bool $succeeded = false,
        string $safeMessage = '',
        array $safeContext = [],
        ?\DateTimeImmutable $createdAt = null,
    ) {
        $this->requestKey = trim($requestKey);
        $this->mutationType = trim($mutationType);
        $this->subjectIdentifier = trim($subjectIdentifier);
        $this->permissionOrRoleKey = trim($permissionOrRoleKey);
        $this->scopeKey = '' !== trim($scopeKey) ? trim($scopeKey) : 'global';
        $this->requestedBySubject = trim($requestedBySubject);
        $this->status = trim($status);
        $this->succeeded = $succeeded;
        $this->safeMessage = $safeMessage;
        $this->safeContext = $safeContext;
        $this->createdAt = $createdAt ?? new \DateTimeImmutable();
    }

    public static function fromEvent(RollingAclMutationExecutionEvent $event): self
    {
        return new self(
            $event->requestKey(),
            $event->mutationType(),
            $event->subjectIdentifier(),
            $event->permissionOrRoleKey(),
            $event->scopeKey(),
            $event->requestedBySubject(),
            $event->status(),
            $event->succeeded(),
            $event->safeMessage(),
            $event->safeContext(),
            $event->createdAt(),
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function requestKey(): string
    {
        return $this->requestKey;
    }

    public function getRequestKey(): string
    {
        return $this->requestKey;
    }

    public function mutationType(): string
    {
        return $this->mutationType;
    }

    public function getMutationType(): string
    {
        return $this->mutationType;
    }

    public function subjectIdentifier(): string
    {
        return $this->subjectIdentifier;
    }

    public function getSubjectIdentifier(): string
    {
        return $this->subjectIdentifier;
    }

    public function permissionOrRoleKey(): string
    {
        return $this->permissionOrRoleKey;
    }

    public function getPermissionOrRoleKey(): string
    {
        return $this->permissionOrRoleKey;
    }

    public function scopeKey(): string
    {
        return $this->scopeKey;
    }

    public function getScopeKey(): string
    {
        return $this->scopeKey;
    }

    public function requestedBySubject(): string
    {
        return $this->requestedBySubject;
    }

    public function getRequestedBySubject(): string
    {
        return $this->requestedBySubject;
    }

    public function status(): string
    {
        return $this->status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function succeeded(): bool
    {
        return $this->succeeded;
    }

    public function isSucceeded(): bool
    {
        return $this->succeeded;
    }

    public function safeMessage(): string
    {
        return $this->safeMessage;
    }

    public function getSafeMessage(): string
    {
        return $this->safeMessage;
    }

    /** @return array<string, mixed> */
    public function safeContext(): array
    {
        return $this->safeContext;
    }

    /** @return array<string, mixed> */
    public function getSafeContext(): array
    {
        return $this->safeContext;
    }

    public function createdAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function toValue(): RollingAclMutationExecutionEvent
    {
        return new RollingAclMutationExecutionEvent(
            $this->requestKey,
            $this->mutationType,
            $this->subjectIdentifier,
            $this->permissionOrRoleKey,
            $this->scopeKey,
            $this->requestedBySubject,
            $this->status,
            $this->succeeded,
            $this->safeMessage,
            $this->safeContext,
            $this->createdAt,
        );
    }
}
