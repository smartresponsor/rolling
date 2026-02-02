<?php
declare(strict_types=1);

namespace App\Acl\Role\Adapter;

use App\Acl\Role\AclSourceInterface;
use PDO;
use src\Entity\Role\{Scope};
use src\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class PdoAclSource implements AclSourceInterface
{
    /**
     * @param \PDO $pdo
     * @param string $bindingsTable
     * @param string $permsTable
     */
    public function __construct(
        private readonly PDO    $pdo,
        private readonly string $bindingsTable = 'role_bindings',
        private readonly string $permsTable = 'role_permissions'
    )
    {
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
        // key format examples: global / tenant:t1 / resource:t1:r1
        $parts = explode(':', $key);
        $conditions = [];
        $params = [':sid' => $subject->value()];

        // global binding
        $conditions[] = "(scope_type = 'global')";

        if ($parts[0] === 'tenant' && isset($parts[1])) {
            $conditions[] = "(scope_type = 'tenant' AND tenant_id = :tenant)";
            $params[':tenant'] = $parts[1];
        } elseif ($parts[0] === 'resource' && isset($parts[1], $parts[2])) {
            $conditions[] = "(scope_type = 'resource' AND tenant_id = :tenant AND resource_id = :res)";
            $params[':tenant'] = $parts[1];
            $params[':res'] = $parts[2];
        }

        $sql = "SELECT DISTINCT role FROM {$this->bindingsTable} WHERE subject_id = :sid AND (" . implode(' OR ', $conditions) . ")";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return array_values(array_map(fn($r) => (string)$r['role'], $stmt->fetchAll(PDO::FETCH_ASSOC)));
    }

    /**
     * @param string $role
     * @return array
     */
    public function permissionsForRole(string $role): array
    {
        $stmt = $this->pdo->prepare("SELECT permission FROM {$this->permsTable} WHERE role = :r");
        $stmt->execute([':r' => $role]);
        return array_values(array_map(fn($r) => (string)$r['permission'], $stmt->fetchAll(PDO::FETCH_ASSOC)));
    }
}
