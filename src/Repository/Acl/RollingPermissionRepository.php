<?php

declare(strict_types=1);

namespace App\Rolling\Repository\Acl;

use App\Rolling\Entity\Acl\RollingPermission;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RollingPermission>
 */
final class RollingPermissionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RollingPermission::class);
    }

    public function findOneByPermissionKey(string $permissionKey): ?RollingPermission
    {
        $permission = $this->findOneBy(['permissionKey' => trim($permissionKey)]);

        return $permission instanceof RollingPermission ? $permission : null;
    }

    public function save(RollingPermission $permission, bool $flush = false): void
    {
        $this->getEntityManager()->persist($permission);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
