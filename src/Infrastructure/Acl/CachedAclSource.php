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

final class CachedAclSource implements AclSourceInterface
{
    private int $ttl;
    private AclSourceInterface $inner;
    /** @var array */
    private array $rolesCache = [];
    /** @var array */
    private array $permCache = [];

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
    {
        $key = $subject->value() . '|' . $scope->key();
        $now = time();
        $hit = $this->rolesCache[$key] ?? null;
        if ($hit && $hit['exp'] > $now) return $hit['roles'];

        $roles = $this->inner->rolesFor($subject, $scope, $ctx);
        $this->rolesCache[$key] = ['exp' => $now + $this->ttl, 'roles' => $roles];
        return $roles;
    }

    /**
     * @param string $role
     * @return array
     */
    public function permissionsForRole(string $role): array
    {
        $key = $role;
        $now = time();
        $hit = $this->permCache[$key] ?? null;
        if ($hit && $hit['exp'] > $now) return $hit['perms'];

        $perms = $this->inner->permissionsForRole($role);
        $this->permCache[$key] = ['exp' => $now + $this->ttl, 'perms' => $perms];
        return $perms;
    }
}
