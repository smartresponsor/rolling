<?php

declare(strict_types=1);

namespace App\Rolling\Repository\Acl;

use App\Rolling\Entity\Acl\RollingAclRule;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RollingAclRule>
 */
final class RollingAclRuleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RollingAclRule::class);
    }

    public function save(RollingAclRule $rule, bool $flush = false): void
    {
        $this->getEntityManager()->persist($rule);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
