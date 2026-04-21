<?php

declare(strict_types=1);

namespace App\Rolling\InfrastructureInterface\Acl;

use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;

interface AclSourceInterface
{
    /** @return list<string> roles (e.g. ["admin","reader"]) */
    public function rolesFor(SubjectId $subject, Scope $scope, array $ctx = []): array;

    /** @return list<string> permissions (e.g. ["message.read","message.delete"]) */
    public function permissionsForRole(string $role): array;
}
