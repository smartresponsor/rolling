<?php

declare(strict_types=1);

use App\Acl\Role\AclSourceInterface;
use src\Entity\Role\{Scope};
use src\Entity\Role\PermissionKey;
use src\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class RoleResolver
{
    /**
     * @param \App\Acl\Role\AclSourceInterface $source
     */
    public function __construct(private readonly AclSourceInterface $source) {}

    /** @return list<string> roles */
    public function subjectRoles(SubjectId $subject, Scope $scope, array $ctx = []): array
    {
        return $this->source->rolesFor($subject, $scope, $ctx);
    }

    /** @return list<string> permissions */
    public function subjectPermissions(SubjectId $subject, Scope $scope, array $ctx = []): array
    {
        $perms = [];
        foreach ($this->subjectRoles($subject, $scope, $ctx) as $r) {
            foreach ($this->source->permissionsForRole($r) as $p) {
                $perms[$p] = true;
            }
        }
        return array_keys($perms);
    }

    /**
     * @param \src\Entity\Role\SubjectId $subject
     * @param \src\Entity\Role\PermissionKey $action
     * @param \src\Entity\Role\Scope $scope
     * @param array $ctx
     * @return bool
     */
    public function can(SubjectId $subject, PermissionKey $action, Scope $scope, array $ctx = []): bool
    {
        $perms = $this->subjectPermissions($subject, $scope, $ctx);
        if (in_array('*', $perms, true)) {
            return true;
        }
        return in_array($action->value(), $perms, true);
    }
}
