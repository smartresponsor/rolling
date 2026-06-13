<?php

declare(strict_types=1);

namespace App\Rolling\RepositoryInterface\Role;

use App\Rolling\Entity\Role\RoleAclRuleEntity;

interface RoleAclRuleRepositoryInterface
{
    public function save(RoleAclRuleEntity $entity, bool $flush = false): void;
}
