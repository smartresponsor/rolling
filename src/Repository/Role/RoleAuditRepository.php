<?php

declare(strict_types=1);

namespace App\Rolling\Repository\Role;

use App\Rolling\Entity\Role\RoleAuditEntity;
use App\Rolling\RepositoryInterface\Role\RoleAuditRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RoleAuditEntity>
 */
final class RoleAuditRepository extends ServiceEntityRepository implements RoleAuditRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoleAuditEntity::class);
    }

    public function save(RoleAuditEntity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /** @return list<RoleAuditEntity> */
    public function latestForSubject(string $subjectId, int $limit = 50): array
    {
        /** @var list<RoleAuditEntity> $rows */
        $rows = $this->createQueryBuilder('audit')
            ->andWhere('audit.subjectId = :subjectId')
            ->setParameter('subjectId', trim($subjectId))
            ->orderBy('audit.timestamp', 'DESC')
            ->setMaxResults(max(1, $limit))
            ->getQuery()
            ->getResult();

        return $rows;
    }
}
