<?php

declare(strict_types=1);

namespace App\Infrastructure\Acl;

<<<<<<< HEAD:src/Infrastructure/Acl/CachedAclSource.php
use App\Entity\Role\SubjectId;
use App\Entity\Role\Scope;

/**
 *
 */

/**
 *
 */
use App\InfrastructureInterface\Acl\AclSourceInterface;

=======
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Acl/Role/CachedAclSource.php
final class CachedAclSource implements AclSourceInterface
{
    /** @var array<string,array{exp:int,roles:array<int,string>}> */
    private array $rolesCache = [];
    /** @var array<string,array{exp:int,perms:array<int,string>}> */
    private array $permCache = [];

<<<<<<< HEAD:src/Infrastructure/Acl/CachedAclSource.php
    /**
     * @param \App\Legacy\Acl\AclSourceInterface $inner
     * @param int $ttlSeconds
     */
    public function __construct(AclSourceInterface $inner, int $ttlSeconds = 300)
    {
        $this->inner = $inner;
        $this->ttl = $ttlSeconds;
    }

    /**
     * @param \App\Entity\Role\SubjectId $subject
     * @param \App\Entity\Role\Scope $scope
     * @param array $ctx
     * @return array
     */
    public function rolesFor(SubjectId $subject, Scope $scope, array $ctx = []): array
=======
    public function __construct(
        private readonly AclSourceInterface $inner,
        private readonly int $ttlSeconds = 300,
    ) {}

    public function rolesFor(\src\Entity\Role\SubjectId $subject, \src\Entity\Role\Scope $scope, array $ctx = []): array
>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Acl/Role/CachedAclSource.php
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

    /**
     * @param array{exp:int}|null $cached
     */
    private function isFresh(?array $cached): bool
    {
        return $cached !== null && $cached['exp'] > time();
    }
}
