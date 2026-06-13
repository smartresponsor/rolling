<?php

declare(strict_types=1);

namespace App\Rolling\Entity\Role;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Rolling\Repository\Role\RoleRepository::class)]
#[ORM\Table(name: 'rolling_role')]
#[ORM\UniqueConstraint(name: 'uniq_rolling_role_key', columns: ['role_key'])]
#[ORM\Index(name: 'idx_rolling_role_enabled', columns: ['enabled'])]
class RoleEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'role_key', type: 'string', length: 160)]
    private string $roleKey;

    #[ORM\Column(name: 'label', type: 'string', length: 180)]
    private string $label;

    #[ORM\Column(name: 'system_role', type: 'boolean')]
    private bool $systemRole = false;

    #[ORM\Column(name: 'enabled', type: 'boolean')]
    private bool $enabled = true;

    public function __construct(string $roleKey = '', string $label = '')
    {
        $this->roleKey = $roleKey;
        $this->label = '' !== $label ? $label : $roleKey;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function label(): string
    {
        return $this->label;
    }

    public function getLabel(): string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = trim($label);

        return $this;
    }

    public function systemRole(): bool
    {
        return $this->systemRole;
    }

    public function isSystemRole(): bool
    {
        return $this->systemRole;
    }

    public function setSystemRole(bool $systemRole): self
    {
        $this->systemRole = $systemRole;

        return $this;
    }

    public function enabled(): bool
    {
        return $this->enabled;
    }

    public function isEnabled(): bool
    {
        return $this->enabled;
    }

    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;

        return $this;
    }
}
