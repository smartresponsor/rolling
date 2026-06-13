<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\Entity\Role\RoleAclMutationExecutionEventEntity;
use App\Rolling\Repository\Role\RoleAclMutationExecutionEventRepository;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationExecutionEventRecorderInterface;
use App\Rolling\Value\Administration\RollingAclMutationExecutionEvent;

/**
 * Persists safe ACL mutation execution events for Administering reports.
 */
final readonly class DoctrineRollingAclMutationExecutionEventRecorder implements RollingAclMutationExecutionEventRecorderInterface
{
    public function __construct(private RoleAclMutationExecutionEventRepository $repository)
    {
    }

    public function record(RollingAclMutationExecutionEvent $event): void
    {
        $this->repository->save(RoleAclMutationExecutionEventEntity::fromEvent($event), true);
    }
}
