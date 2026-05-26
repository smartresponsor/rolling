<?php

declare(strict_types=1);

namespace App\Rolling\Repository\Acl;

use App\Rolling\Entity\Acl\RollingSubjectRoleAssignment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RollingSubjectRoleAssignment>
 */
final class RollingSubjectRoleAssignmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RollingSubjectRoleAssignment::class);
    }

    public function findOneAssignment(string $subjectIdentifier, string $roleKey, string $scopeKey): ?RollingSubjectRoleAssignment
    {
        $assignment = $this->findOneBy([
            'subjectIdentifier' => trim($subjectIdentifier),
            'roleKey' => trim($roleKey),
            'scopeKey' => trim($scopeKey),
        ]);

        return $assignment instanceof RollingSubjectRoleAssignment ? $assignment : null;
    }

    public function save(RollingSubjectRoleAssignment $assignment, bool $flush = false): void
    {
        $this->getEntityManager()->persist($assignment);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(RollingSubjectRoleAssignment $assignment, bool $flush = false): void
    {
        $this->getEntityManager()->remove($assignment);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
