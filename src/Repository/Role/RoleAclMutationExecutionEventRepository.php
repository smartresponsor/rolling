<?php

declare(strict_types=1);

namespace App\Rolling\Repository\Role;

use App\Rolling\Entity\Role\RoleAclMutationExecutionEventEntity;
use App\Rolling\RepositoryInterface\Role\RoleAclMutationExecutionEventRepositoryInterface;
use App\Rolling\Value\Administration\RollingAclMutationExecutionFilter;
use App\Rolling\Value\Administration\RollingAclMutationExecutionSummary;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RollingAclMutationExecutionEventEntity>
 */
final class RoleAclMutationExecutionEventRepository extends ServiceEntityRepository implements RoleAclMutationExecutionEventRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoleAclMutationExecutionEventEntity::class);
    }

    public function save(RoleAclMutationExecutionEventEntity $event, bool $flush = false): void
    {
        $this->getEntityManager()->persist($event);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /** @return list<RollingAclMutationExecutionEventEntity> */
    public function matchingByFilter(RollingAclMutationExecutionFilter $filter): array
    {
        $qb = $this->createQueryBuilder('event')
            ->orderBy('event.createdAt', 'DESC')
            ->setMaxResults($filter->limit());

        if (null !== $filter->mutationType() && '' !== trim($filter->mutationType())) {
            $qb->andWhere('event.mutationType = :mutationType')
                ->setParameter('mutationType', trim($filter->mutationType()));
        }

        if (null !== $filter->status() && '' !== trim($filter->status())) {
            $qb->andWhere('event.status = :status')
                ->setParameter('status', trim($filter->status()));
        }

        if (null !== $filter->subjectIdentifier() && '' !== trim($filter->subjectIdentifier())) {
            $qb->andWhere('event.subjectIdentifier = :subjectIdentifier')
                ->setParameter('subjectIdentifier', trim($filter->subjectIdentifier()));
        }

        /** @var list<RollingAclMutationExecutionEventEntity> $events */
        $events = $qb->getQuery()->getResult();

        return $events;
    }

    public function summary(RollingAclMutationExecutionFilter $filter): RollingAclMutationExecutionSummary
    {
        $events = $this->matchingByFilter($filter);
        $countByStatus = [];
        $countByMutationType = [];
        $latestAt = null;

        foreach ($events as $event) {
            $countByStatus[$event->status()] = ($countByStatus[$event->status()] ?? 0) + 1;
            $countByMutationType[$event->mutationType()] = ($countByMutationType[$event->mutationType()] ?? 0) + 1;

            if (null === $latestAt || $event->createdAt() > $latestAt) {
                $latestAt = $event->createdAt();
            }
        }

        return new RollingAclMutationExecutionSummary(count($events), $countByStatus, $countByMutationType, $latestAt);
    }
}
