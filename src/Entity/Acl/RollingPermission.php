<?php

declare(strict_types=1);

namespace App\Rolling\Entity\Acl;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'rolling_permission')]
#[ORM\UniqueConstraint(name: 'uniq_rolling_permission_key', columns: ['permission_key'])]
class RollingPermission
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'permission_key', type: 'string', length: 180)]
    private string $permissionKey;

    #[ORM\Column(name: 'component_name', type: 'string', length: 120)]
    private string $componentName;

    #[ORM\Column(name: 'description', type: 'text', nullable: true)]
    private ?string $description = null;

    public function __construct(string $permissionKey = '', string $componentName = '')
    {
        $this->permissionKey = $permissionKey;
        $this->componentName = $componentName;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function componentName(): string
    {
        return $this->componentName;
    }

    public function getComponentName(): string
    {
        return $this->componentName;
    }

    public function setComponentName(string $componentName): self
    {
        $this->componentName = trim($componentName);

        return $this;
    }

    public function description(): ?string
    {
        return $this->description;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $description = null === $description ? null : trim($description);
        $this->description = '' === $description ? null : $description;

        return $this;
    }
}
