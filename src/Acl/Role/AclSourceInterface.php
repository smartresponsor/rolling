<?php

declare(strict_types=1);

namespace App\Acl\Role;

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
