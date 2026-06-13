<?php

declare(strict_types=1);

namespace App\Rolling\RepositoryInterface\Role;

use App\Rolling\Entity\Role\RoleEntity;

interface RoleRepositoryInterface
{
    public function save(RoleEntity $entity, bool $flush = false): void;
}
