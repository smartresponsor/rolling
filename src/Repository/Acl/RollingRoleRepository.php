<?php

declare(strict_types=1);

namespace App\Rolling\Repository\Acl;

use App\Rolling\Entity\Acl\RollingRole;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RollingRole>
 */
final class RollingRoleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RollingRole::class);
    }

    public function findOneByRoleKey(string $roleKey): ?RollingRole
    {
        $role = $this->findOneBy(['roleKey' => trim($roleKey)]);

        return $role instanceof RollingRole ? $role : null;
    }

    public function requireEnabled(string $roleKey): ?RollingRole
    {
        $role = $this->findOneByRoleKey($roleKey);

        return null !== $role && $role->enabled() ? $role : null;
    }

    public function save(RollingRole $role, bool $flush = false): void
    {
        $this->getEntityManager()->persist($role);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
