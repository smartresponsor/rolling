<?php

declare(strict_types=1);

namespace App\Legacy\Service;

use App\InfrastructureInterface\Acl\AclSourceInterface;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;

final class RoleResolver
{
<<<<<<< HEAD:src/Legacy/Service/RoleResolver.php
    public function __construct(private readonly AclSourceInterface $source)
    {
    }
=======
    /**
     * @param \App\Acl\Role\AclSourceInterface $source
     */
    public function __construct(private readonly AclSourceInterface $source) {}
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Service/Role/RoleResolver.php

    /** @return list<string> */
    public function subjectRoles(SubjectId $subject, Scope $scope, array $ctx = []): array
    {
        return $this->source->rolesFor($subject, $scope, $ctx);
    }

    /** @return list<string> */
    public function subjectPermissions(SubjectId $subject, Scope $scope, array $ctx = []): array
    {
        $perms = [];
        foreach ($this->subjectRoles($subject, $scope, $ctx) as $role) {
            foreach ($this->source->permissionsForRole($role) as $permission) {
                $perms[$permission] = true;
            }
        }

        return array_keys($perms);
    }

    public function can(SubjectId $subject, PermissionKey $action, Scope $scope, array $ctx = []): bool
    {
        $perms = $this->subjectPermissions($subject, $scope, $ctx);
<<<<<<< HEAD:src/Legacy/Service/RoleResolver.php

        if (in_array('*', $perms, true)) {
            return true;
        }

=======
        if (in_array('*', $perms, true)) {
            return true;
        }
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Service/Role/RoleResolver.php
        return in_array($action->value(), $perms, true);
    }
}
