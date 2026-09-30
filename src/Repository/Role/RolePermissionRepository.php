<?php

declare(strict_types=1);

namespace App\Rolling\Repository\Role;

use App\Rolling\Entity\Role\RolePermissionEntity;
use App\Rolling\RepositoryInterface\Role\RolePermissionRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RollingRolePermission>
 */
final class RolePermissionRepository extends ServiceEntityRepository implements RolePermissionRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RolePermissionEntity::class);
    }

    /** Finds matching repository values for the supplied criteria. */
    public function findOneGrant(string $roleKey, string $permissionKey, string $scopePattern): ?RolePermissionEntity
    {
        $grant = $this->findOneBy([
            'roleKey' => trim($roleKey),
            'permissionKey' => trim($permissionKey),
            'scopePattern' => trim($scopePattern),
        ]);

        return $grant instanceof RolePermissionEntity ? $grant : null;
    }

    /** Persists the supplied value in the repository. */
    public function save(RolePermissionEntity $grant, bool $flush = false): void
    {
        $this->getEntityManager()->persist($grant);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /** Executes the remove operation. */
    public function remove(RolePermissionEntity $grant, bool $flush = false): void
    {
        $this->getEntityManager()->remove($grant);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
