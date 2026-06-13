<?php

declare(strict_types=1);

namespace App\Rolling\Entity\Role;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Runtime ACL decision audit row owned by Rolling.
 *
 * This entity replaces the retired ops/db/sqlite/role_audit.sql schema file.
 */
#[ORM\Entity(repositoryClass: \App\Rolling\Repository\Role\RoleAuditRepository::class)]
#[ORM\Table(name: 'role_audit')]
#[ORM\Index(name: 'idx_role_audit_ts', columns: ['ts'])]
#[ORM\Index(name: 'idx_role_audit_subject', columns: ['subject_id'])]
#[ORM\Index(name: 'idx_role_audit_action', columns: ['action'])]
#[ORM\Index(name: 'idx_role_audit_scope', columns: ['scope_key'])]
#[ORM\Index(name: 'idx_role_audit_decision', columns: ['decision'])]
final class RoleAuditEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'ts', type: 'integer')]
    private int $timestamp;

    #[ORM\Column(name: 'subject_id', type: 'string', length: 220)]
    private string $subjectId;

    #[ORM\Column(name: 'action', type: 'string', length: 180)]
    private string $action;

    #[ORM\Column(name: 'scope_key', type: 'string', length: 220)]
    private string $scopeKey;

    #[ORM\Column(name: 'decision', type: 'string', length: 40)]
    private string $decision;

    #[ORM\Column(name: 'reason', type: 'text')]
    private string $reason = '';

    /** @var array<string, mixed> */
    #[ORM\Column(name: 'obligations', type: Types::JSON)]
    private array $obligations = [];

    /** @var array<string, mixed> */
    #[ORM\Column(name: 'ctx', type: Types::JSON)]
    private array $context = [];

    /** @param array<string, mixed> $obligations @param array<string, mixed> $context */
    public function __construct(
        int $timestamp = 0,
        string $subjectId = '',
        string $action = '',
        string $scopeKey = 'global',
        string $decision = 'unknown',
        string $reason = '',
        array $obligations = [],
        array $context = [],
    ) {
        $this->timestamp = $timestamp > 0 ? $timestamp : time();
        $this->subjectId = trim($subjectId);
        $this->action = trim($action);
        $this->scopeKey = '' !== trim($scopeKey) ? trim($scopeKey) : 'global';
        $this->decision = '' !== trim($decision) ? trim($decision) : 'unknown';
        $this->reason = trim($reason);
        $this->obligations = $obligations;
        $this->context = $context;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function timestamp(): int
    {
        return $this->timestamp;
    }

    public function getTimestamp(): int
    {
        return $this->timestamp;
    }

    public function subjectId(): string
    {
        return $this->subjectId;
    }

    public function getSubjectId(): string
    {
        return $this->subjectId;
    }

    public function action(): string
    {
        return $this->action;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function scopeKey(): string
    {
        return $this->scopeKey;
    }

    public function getScopeKey(): string
    {
        return $this->scopeKey;
    }

    public function decision(): string
    {
        return $this->decision;
    }

    public function getDecision(): string
    {
        return $this->decision;
    }

    public function reason(): string
    {
        return $this->reason;
    }

    public function getReason(): string
    {
        return $this->reason;
    }

    /** @return array<string, mixed> */
    public function obligations(): array
    {
        return $this->obligations;
    }

    /** @return array<string, mixed> */
    public function getObligations(): array
    {
        return $this->obligations;
    }

    /** @return array<string, mixed> */
    public function context(): array
    {
        return $this->context;
    }

    /** @return array<string, mixed> */
    public function getContext(): array
    {
        return $this->context;
    }
}
