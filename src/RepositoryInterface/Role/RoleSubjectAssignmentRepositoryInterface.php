<?php

declare(strict_types=1);

namespace App\Rolling\RepositoryInterface\Role;

use App\Rolling\Entity\Role\RoleSubjectAssignmentEntity;

interface RoleSubjectAssignmentRepositoryInterface
{
    public function save(RoleSubjectAssignmentEntity $entity, bool $flush = false): void;
}
