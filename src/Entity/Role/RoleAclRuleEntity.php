<?php

declare(strict_types=1);

namespace App\Rolling\Entity\Role;

use App\Objecting\EntityInterface\ObjectStatefulInterface;
use App\Objecting\EntityTrait\Embeddable\ObjectStateEmbeddableTrait;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Rolling\Repository\Role\RoleAclRuleRepository::class)]
#[ORM\Table(name: 'rolling_acl_rule')]
#[ORM\Index(name: 'idx_rolling_acl_rule_object_enabled', columns: ['object_enabled'])]
#[ORM\Index(name: 'idx_rolling_acl_rule_subject', columns: ['subject_identifier'])]
#[ORM\Index(name: 'idx_rolling_acl_rule_permission', columns: ['permission_key'])]
#[ORM\Index(name: 'idx_rolling_acl_rule_subject_permission', columns: ['subject_identifier', 'permission_key'])]
class RoleAclRuleEntity implements ObjectStatefulInterface
{
    use ObjectStateEmbeddableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'subject_identifier', type: 'string', length: 220)]
    private string $subjectIdentifier;

    #[ORM\Column(name: 'permission_key', type: 'string', length: 180)]
    private string $permissionKey;

    #[ORM\Column(name: 'scope_key', type: 'string', length: 220)]
    private string $scopeKey;

    #[ORM\Column(name: 'effect', type: 'string', length: 20)]
    private string $effect;

    /** @var array<string, mixed> */
    #[ORM\Column(name: 'conditions', type: Types::JSON)]
    private array $conditions = [];

    /** @param array<string, mixed> $conditions */
    public function __construct(string $subjectIdentifier = '', string $permissionKey = '', string $scopeKey = 'global', string $effect = 'allow', array $conditions = [])
    {
        $this->subjectIdentifier = $subjectIdentifier;
        $this->permissionKey = $permissionKey;
        $this->scopeKey = '' !== $scopeKey ? $scopeKey : 'global';
        $this->effect = in_array($effect, ['allow', 'deny'], true) ? $effect : 'allow';
        $this->conditions = $conditions;
        $this->initializeObjectState();
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

    public function permissionKey(): string
    {
        return $this->permissionKey;
    }

    public function getPermissionKey(): string
    {
        return $this->permissionKey;
    }

    public function setPermissionKey(string $permissionKey): self
    {
        $this->permissionKey = trim($permissionKey);

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

    public function effect(): string
    {
        return $this->effect;
    }

    public function getEffect(): string
    {
        return $this->effect;
    }

    public function setEffect(string $effect): self
    {
        $effect = trim($effect);
        $this->effect = in_array($effect, ['allow', 'deny'], true) ? $effect : 'allow';

        return $this;
    }

    /** @return array<string, mixed> */
    public function conditions(): array
    {
        return $this->conditions;
    }

    /** @return array<string, mixed> */
    public function getConditions(): array
    {
        return $this->conditions;
    }

    /** @param array<string, mixed> $conditions */
    public function setConditions(array $conditions): self
    {
        $this->conditions = $conditions;

        return $this;
    }

    public function enabled(): bool
    {
        return $this->isObjectEnabled();
    }

    public function isEnabled(): bool
    {
        return $this->isObjectEnabled();
    }

    public function setEnabled(bool $enabled): self
    {
        $this->setObjectEnabled($enabled);

        return $this;
    }
}
