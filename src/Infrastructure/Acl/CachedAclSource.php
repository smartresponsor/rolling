<?php

declare(strict_types=1);

namespace App\Infrastructure\Acl;

use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;
use App\InfrastructureInterface\Acl\AclSourceInterface;

final class CachedAclSource implements AclSourceInterface
{
    /** @var array<string,array{exp:int,roles:list<string>}> */
    private array $rolesCache = [];

    /** @var array<string,array{exp:int,perms:list<string>}> */
    private array $permCache = [];

    public function __construct(
        private readonly AclSourceInterface $inner,
        private readonly int $ttlSeconds = 300,
    ) {
    }

    public function rolesFor(SubjectId $subject, Scope $scope, array $ctx = []): array
    {
        $key = $subject->value() . '|' . $scope->key();
        $cached = $this->rolesCache[$key] ?? null;
        if ($this->isFresh($cached)) {
            return $cached['roles'];
        }

        $roles = $this->inner->rolesFor($subject, $scope, $ctx);
        $this->rolesCache[$key] = [
            'exp' => time() + $this->ttlSeconds,
            'roles' => $roles,
        ];

        return $roles;
    }

    public function permissionsForRole(string $role): array
    {
        $cached = $this->permCache[$role] ?? null;
        if ($this->isFresh($cached)) {
            return $cached['perms'];
        }

        $perms = $this->inner->permissionsForRole($role);
        $this->permCache[$role] = [
            'exp' => time() + $this->ttlSeconds,
            'perms' => $perms,
        ];

        return $perms;
    }

    /** @param array{exp:int}|null $cached */
    private function isFresh(?array $cached): bool
    {
        return $cached !== null && $cached['exp'] > time();
    }
}
