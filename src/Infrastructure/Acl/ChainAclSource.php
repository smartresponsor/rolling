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
    /** @param list<AclSourceInterface> $sources */
    public function __construct(private readonly array $sources) {}

    /**
     * @param \App\Entity\Role\SubjectId $subject
     * @param \App\Entity\Role\Scope $scope
     * @param array $ctx
     * @return array
     */
    public function rolesFor(SubjectId $subject, Scope $scope, array $ctx = []): array
    {
        $roles = [];
        foreach ($this->sources as $source) {
            array_push($roles, ...$source->rolesFor($subject, $scope, $ctx));
        }

        return $this->uniqueValues($roles);
    }

    public function permissionsForRole(string $role): array
    {
        $permissions = [];
        foreach ($this->sources as $source) {
            array_push($permissions, ...$source->permissionsForRole($role));
        }

        return $this->uniqueValues($permissions);
    }

    /**
     * @param list<string> $values
     * @return list<string>
     */
    private function uniqueValues(array $values): array
    {
        $unique = [];
        foreach ($values as $value) {
            $unique[$value] = true;
        }

        /** @var list<string> */
        return array_keys($unique);
    }
}
