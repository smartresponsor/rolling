<?php

declare(strict_types=1);

namespace App\Rolling\Entity\Role;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Rolling\Repository\Role\RolePermissionRepository::class)]
#[ORM\Table(name: 'rolling_role_permission')]
#[ORM\UniqueConstraint(name: 'uniq_rolling_role_permission', columns: ['role_key', 'permission_key', 'scope_pattern'])]
#[ORM\Index(name: 'idx_rolling_role_permission_role', columns: ['role_key'])]
#[ORM\Index(name: 'idx_rolling_role_permission_permission', columns: ['permission_key'])]
class RolePermissionEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'role_key', type: 'string', length: 160)]
    private string $roleKey;

    #[ORM\Column(name: 'permission_key', type: 'string', length: 180)]
    private string $permissionKey;

    #[ORM\Column(name: 'scope_pattern', type: 'string', length: 220)]
    private string $scopePattern = 'global';

    #[ORM\Column(name: 'effect', type: 'string', length: 20)]
    private string $effect = 'allow';

    public function __construct(string $roleKey = '', string $permissionKey = '', string $scopePattern = 'global')
    {
        $this->roleKey = $roleKey;
        $this->permissionKey = $permissionKey;
        $this->scopePattern = '' !== $scopePattern ? $scopePattern : 'global';
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

    public function scopePattern(): string
    {
        return $this->scopePattern;
    }

    public function getScopePattern(): string
    {
        return $this->scopePattern;
    }

    public function setScopePattern(string $scopePattern): self
    {
        $scopePattern = trim($scopePattern);
        $this->scopePattern = '' !== $scopePattern ? $scopePattern : 'global';

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
}
