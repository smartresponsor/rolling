<?php

declare(strict_types=1);

namespace App\Rolling\Repository\Role;

use App\Rolling\Entity\Role\RoleAclRuleEntity;
use App\Rolling\RepositoryInterface\Role\RoleAclRuleRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RollingAclRule>
 */
final class RoleAclRuleRepository extends ServiceEntityRepository implements RoleAclRuleRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoleAclRuleEntity::class);
    }

    public function save(RoleAclRuleEntity $rule, bool $flush = false): void
    {
        $this->getEntityManager()->persist($rule);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
