<?php
declare(strict_types=1);

namespace App\Acl\Role;

use App\Entity\Role\App\src\Entity\Role\SubjectId;
use App\Entity\Role\App\src\Entity\Role\Scope;

/**
 *
 */

/**
 *
 */
interface AclSourceInterface
{
    /** @return list<string> roles (e.g. ["admin","reader"]) */
    public function rolesFor(\src\Entity\Role\SubjectId $subject, \src\Entity\Role\Scope $scope, array $ctx = []): array;

    /** @return list<string> permissions (e.g. ["message.read","message.delete"]) */
    public function permissionsForRole(string $role): array;
}
