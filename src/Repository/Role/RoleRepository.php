<?php

declare(strict_types=1);

namespace App\Rolling\Repository\Role;

use App\Rolling\Entity\Role\RoleEntity;
use App\Rolling\RepositoryInterface\Role\RoleRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RollingRole>
 */
final class RoleRepository extends ServiceEntityRepository implements RoleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoleEntity::class);
    }

    public function findOneByRoleKey(string $roleKey): ?RoleEntity
    {
        $role = $this->findOneBy(['roleKey' => trim($roleKey)]);

        return $role instanceof RoleEntity ? $role : null;
    }

    public function requireEnabled(string $roleKey): ?RoleEntity
    {
        $role = $this->findOneByRoleKey($roleKey);

        return null !== $role && $role->enabled() ? $role : null;
    }

    public function save(RoleEntity $role, bool $flush = false): void
    {
        $this->getEntityManager()->persist($role);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
