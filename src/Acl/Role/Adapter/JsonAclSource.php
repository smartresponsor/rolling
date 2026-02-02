<?php
declare(strict_types=1);

namespace App\Acl\Role\Adapter;

use App\Acl\Role\AclSourceInterface;
use src\Entity\Role\{Scope};
use src\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class JsonAclSource implements AclSourceInterface
{
    /** @var array */
    private array $cfg;

    /**
     * @param array|string $cfgOrPath
     */
    public function __construct(array|string $cfgOrPath)
    {
        if (is_string($cfgOrPath)) {
            $data = json_decode((string)@file_get_contents($cfgOrPath), true);
            $this->cfg = is_array($data) ? $data : ['roles' => [], 'bindings' => []];
        } else {
            $this->cfg = $cfgOrPath;
        }
    }

    /**
     * @param \src\Entity\Role\SubjectId $subject
     * @param \src\Entity\Role\Scope $scope
     * @param array $ctx
     * @return array
     */
    public function rolesFor(SubjectId $subject, Scope $scope, array $ctx = []): array
    {
        $key = $scope->key();
        $roles = [];
        foreach ((array)($this->cfg['bindings'] ?? []) as $b) {
            if (($b['subjectId'] ?? null) !== $subject->value()) continue;
            if (($b['scope'] ?? '') !== $key && ($b['scope'] ?? '') !== 'global') continue;
            $roles[] = (string)$b['role'];
        }
        return array_values(array_unique($roles));
    }

    /**
     * @param string $role
     * @return array
     */
    public function permissionsForRole(string $role): array
    {
        $perms = (array)(($this->cfg['roles'] ?? [])[$role] ?? []);
        return array_values(array_unique(array_map('strval', $perms)));
    }
}
