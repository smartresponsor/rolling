<?php

declare(strict_types=1);

namespace App\InfrastructureInterface\Acl;

<<<<<<< HEAD:src/InfrastructureInterface/Acl/AclSourceInterface.php
use App\Entity\Role\SubjectId;
use App\Entity\Role\Scope;

=======
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Acl/Role/AclSourceInterface.php
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
