<?php

declare(strict_types=1);

namespace App\Rolling\RepositoryInterface\Role;

use App\Rolling\Entity\Role\RoleAclMutationExecutionEventEntity;

interface RoleAclMutationExecutionEventRepositoryInterface
{
    public function save(RoleAclMutationExecutionEventEntity $entity, bool $flush = false): void;
}
