<?php

declare(strict_types=1);

namespace App\Rolling\Repository\Role;

use App\Rolling\Entity\Role\RoleHierarchyEntity;
use App\Rolling\RepositoryInterface\Role\RoleHierarchyRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RoleHierarchyEntity>
 */
final class RoleHierarchyRepository extends ServiceEntityRepository implements RoleHierarchyRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoleHierarchyEntity::class);
    }

    public function findOneEdge(string $parentRoleKey, string $childRoleKey): ?RoleHierarchyEntity
    {
        $edge = $this->findOneBy([
            'parentRoleKey' => trim($parentRoleKey),
            'childRoleKey' => trim($childRoleKey),
        ]);

        return $edge instanceof RoleHierarchyEntity ? $edge : null;
    }

    public function save(RoleHierarchyEntity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
