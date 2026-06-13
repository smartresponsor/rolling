<?php

declare(strict_types=1);

namespace App\Rolling\RepositoryInterface\Role;

use App\Rolling\Entity\Role\RolePermissionEntity;

interface RolePermissionRepositoryInterface
{
    public function save(RolePermissionEntity $entity, bool $flush = false): void;
}
