<?php

declare(strict_types=1);

namespace App\Rolling\Entity\Acl;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'rolling_role_hierarchy')]
#[ORM\UniqueConstraint(name: 'uniq_rolling_role_hierarchy_edge', columns: ['parent_role_key', 'child_role_key'])]
class RollingRoleHierarchy
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'parent_role_key', type: 'string', length: 160)]
    private string $parentRoleKey;

    #[ORM\Column(name: 'child_role_key', type: 'string', length: 160)]
    private string $childRoleKey;

    #[ORM\Column(name: 'enabled', type: 'boolean')]
    private bool $enabled = true;

    public function __construct(string $parentRoleKey, string $childRoleKey)
    {
        $this->parentRoleKey = $parentRoleKey;
        $this->childRoleKey = $childRoleKey;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function parentRoleKey(): string
    {
        return $this->parentRoleKey;
    }

    public function childRoleKey(): string
    {
        return $this->childRoleKey;
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

    public function enable(): self
    {
        return $this->setEnabled(true);
    }

    public function disable(): self
    {
        return $this->setEnabled(false);
    }
}
