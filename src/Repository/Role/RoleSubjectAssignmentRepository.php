<?php

declare(strict_types=1);

namespace App\Rolling\Repository\Role;

use App\Rolling\Entity\Role\RoleSubjectAssignmentEntity;
use App\Rolling\RepositoryInterface\Role\RoleSubjectAssignmentRepositoryInterface;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<RollingSubjectRoleAssignment>
 */
final class RoleSubjectAssignmentRepository extends ServiceEntityRepository implements RoleSubjectAssignmentRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RoleSubjectAssignmentEntity::class);
    }

    /** Finds matching repository values for the supplied criteria. */
    public function findOneAssignment(string $subjectIdentifier, string $roleKey, string $scopeKey): ?RoleSubjectAssignmentEntity
    {
        $assignment = $this->findOneBy([
            'subjectIdentifier' => trim($subjectIdentifier),
            'roleKey' => trim($roleKey),
            'scopeKey' => trim($scopeKey),
        ]);

        return $assignment instanceof RoleSubjectAssignmentEntity ? $assignment : null;
    }

    /** Persists the supplied value in the repository. */
    public function save(RoleSubjectAssignmentEntity $assignment, bool $flush = false): void
    {
        $this->getEntityManager()->persist($assignment);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /** Executes the remove operation. */
    public function remove(RoleSubjectAssignmentEntity $assignment, bool $flush = false): void
    {
        $this->getEntityManager()->remove($assignment);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
}
