<?php

declare(strict_types=1);

namespace App\Rolling\RepositoryInterface\Role;

use App\Rolling\Entity\Role\RoleAuditEntity;

interface RoleAuditRepositoryInterface
{
    public function save(RoleAuditEntity $entity, bool $flush = false): void;
}
