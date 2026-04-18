<?php

declare(strict_types=1);

namespace App\InfrastructureInterface\Acl;

use App\Entity\Role\SubjectId;
use App\Entity\Role\Scope;

/**
 *
 */

/**
 *
 */
interface AclSourceInterface
{
    /** @return list<string> roles (e.g. ["admin","reader"]) */
    public function rolesFor(SubjectId $subject, Scope $scope, array $ctx = []): array;

    /** @return list<string> permissions (e.g. ["message.read","message.delete"]) */
    public function permissionsForRole(string $role): array;
}
