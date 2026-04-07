<?php
declare(strict_types=1);

namespace App\Infrastructure\Acl;

use App\Entity\Role\SubjectId;
use App\Entity\Role\Scope;

/**
 *
 */

/**
 *
 */
use App\InfrastructureInterface\Acl\AclSourceInterface;

final class ChainAclSource implements AclSourceInterface
{
    /**
     * @param array $sources
     */
    public function __construct(private readonly array $sources)
    {
    }

    /**
     * @param \App\Entity\Role\SubjectId $subject
     * @param \App\Entity\Role\Scope $scope
     * @param array $ctx
     * @return array
     */
    public function rolesFor(SubjectId $subject, Scope $scope, array $ctx = []): array
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
