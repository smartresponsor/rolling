<?php

declare(strict_types=1);

namespace App\Acl\Role;

/**
 *
 */

/**
 *
 */
final class ChainAclSource implements AclSourceInterface
{
    /**
     * @param array $sources
     */
    public function __construct(private readonly array $sources) {}

    /**
     * @param \src\Entity\Role\SubjectId $subject
     * @param \src\Entity\Role\Scope $scope
     * @param array $ctx
     * @return array
     */
    public function rolesFor(\src\Entity\Role\SubjectId $subject, \src\Entity\Role\Scope $scope, array $ctx = []): array
    {
        $set = [];
        foreach ($this->sources as $s) {
            foreach ($s->rolesFor($subject, $scope, $ctx) as $r) {
                $set[$r] = true;
            }
        }
        return array_keys($set);
    }

    /**
     * @param string $role
     * @return array
     */
    public function permissionsForRole(string $role): array
    {
        $set = [];
        foreach ($this->sources as $s) {
            foreach ($s->permissionsForRole($role) as $p) {
                $set[$p] = true;
            }
        }
        return array_keys($set);
    }
}
