<?php

declare(strict_types=1);

namespace App\Rolling\Repository\Acl;

use App\Rolling\Entity\Acl\RollingRolePermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RollingRolePermission>
 */
final class RollingRolePermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RollingRolePermission::class);
    }

    public function findOneGrant(string $roleKey, string $permissionKey, string $scopePattern): ?RollingRolePermission
    {
        $grant = $this->findOneBy([
            'roleKey' => trim($roleKey),
            'permissionKey' => trim($permissionKey),
            'scopePattern' => trim($scopePattern),
        ]);

        return $grant instanceof RollingRolePermission ? $grant : null;
    }

    public function save(RollingRolePermission $grant, bool $flush = false): void
    {
        $this->getEntityManager()->persist($grant);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RollingRolePermission $grant, bool $flush = false): void
    {
        $this->getEntityManager()->remove($grant);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
