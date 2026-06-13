<?php

declare(strict_types=1);

namespace App\Rolling\RepositoryInterface\Role;

use App\Rolling\Entity\Role\RoleHierarchyEntity;

interface RoleHierarchyRepositoryInterface
{
    public function save(RoleHierarchyEntity $entity, bool $flush = false): void;
}
