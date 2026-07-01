<?php

declare(strict_types=1);

namespace App\Rolling\Entity\Role;

use App\Objecting\EntityInterface\ObjectStatefulInterface;
use App\Objecting\EntityTrait\Embeddable\ObjectStateEmbeddableTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: \App\Rolling\Repository\Role\RoleHierarchyRepository::class)]
#[ORM\Table(name: 'rolling_role_hierarchy')]
#[ORM\UniqueConstraint(name: 'uniq_rolling_role_hierarchy_edge', columns: ['parent_role_key', 'child_role_key'])]
#[ORM\Index(name: 'idx_rolling_role_hierarchy_parent', columns: ['parent_role_key'])]
#[ORM\Index(name: 'idx_rolling_role_hierarchy_child', columns: ['child_role_key'])]
#[ORM\Index(name: 'idx_rolling_role_hierarchy_object_enabled', columns: ['object_enabled'])]
class RoleHierarchyEntity implements ObjectStatefulInterface
{
    use ObjectStateEmbeddableTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'parent_role_key', type: 'string', length: 160)]
    private string $parentRoleKey;

    #[ORM\Column(name: 'child_role_key', type: 'string', length: 160)]
    private string $childRoleKey;

    public function __construct(string $parentRoleKey, string $childRoleKey)
    {
        $this->parentRoleKey = $parentRoleKey;
        $this->childRoleKey = $childRoleKey;
        $this->initializeObjectState();
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

    public function enable(): self
    {
        return $this->setEnabled(true);
    }

    public function disable(): self
    {
        return $this->setEnabled(false);
    }
}
