<?php

declare(strict_types=1);

namespace App\Rolling\Entity\Acl;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'rolling_subject_role_assignment')]
#[ORM\UniqueConstraint(name: 'uniq_rolling_subject_role_scope', columns: ['subject_identifier', 'role_key', 'scope_key'])]
class RollingSubjectRoleAssignment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'subject_identifier', type: 'string', length: 220)]
    private string $subjectIdentifier;

    #[ORM\Column(name: 'role_key', type: 'string', length: 160)]
    private string $roleKey;

    #[ORM\Column(name: 'scope_key', type: 'string', length: 220)]
    private string $scopeKey = 'global';

    #[ORM\Column(name: 'assigned_at', type: 'datetime_immutable')]
    private \DateTimeImmutable $assignedAt;

    public function __construct(string $subjectIdentifier = '', string $roleKey = '', string $scopeKey = 'global')
    {
        $this->subjectIdentifier = $subjectIdentifier;
        $this->roleKey = $roleKey;
        $this->scopeKey = '' !== $scopeKey ? $scopeKey : 'global';
        $this->assignedAt = new \DateTimeImmutable();
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function subjectIdentifier(): string
    {
        return $this->subjectIdentifier;
    }

    public function getSubjectIdentifier(): string
    {
        return $this->subjectIdentifier;
    }

    public function setSubjectIdentifier(string $subjectIdentifier): self
    {
        $this->subjectIdentifier = trim($subjectIdentifier);

        return $this;
    }

    public function roleKey(): string
    {
        return $this->roleKey;
    }

    public function getRoleKey(): string
    {
        return $this->roleKey;
    }

    public function setRoleKey(string $roleKey): self
    {
        $this->roleKey = trim($roleKey);

        return $this;
    }

    public function scopeKey(): string
    {
        return $this->scopeKey;
    }

    public function getScopeKey(): string
    {
        return $this->scopeKey;
    }

    public function setScopeKey(string $scopeKey): self
    {
        $scopeKey = trim($scopeKey);
        $this->scopeKey = '' !== $scopeKey ? $scopeKey : 'global';

        return $this;
    }

    public function assignedAt(): \DateTimeImmutable
    {
        return $this->assignedAt;
    }

    public function getAssignedAt(): \DateTimeImmutable
    {
        return $this->assignedAt;
    }
}
