<?php

declare(strict_types=1);

namespace App\Legacy\Service;

use App\InfrastructureInterface\Acl\AclSourceInterface;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;

final class RoleResolver
{
    public function __construct(private readonly AclSourceInterface $source)
    {
    }

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

        if (in_array('*', $perms, true)) {
            return true;
        }

        return in_array($action->value(), $perms, true);
    }
}
