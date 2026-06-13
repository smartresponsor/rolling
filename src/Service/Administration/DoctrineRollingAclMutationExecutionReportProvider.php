<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\Repository\Role\RoleAclMutationExecutionEventRepository;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationExecutionReportProviderInterface;
use App\Rolling\Value\Administration\RollingAclMutationExecutionFilter;
use App\Rolling\Value\Administration\RollingAclMutationExecutionReport;

/**
 * Doctrine-backed metadata-only execution report provider.
 */
final readonly class DoctrineRollingAclMutationExecutionReportProvider implements RollingAclMutationExecutionReportProviderInterface
{
    public function __construct(private RoleAclMutationExecutionEventRepository $repository)
    {
    }

    public function report(RollingAclMutationExecutionFilter $filter): RollingAclMutationExecutionReport
    {
        $events = array_map(
            static fn ($event) => $event->toValue(),
            $this->repository->matchingByFilter($filter),
        );

        return new RollingAclMutationExecutionReport($filter, $this->repository->summary($filter), $events);
    }
}
