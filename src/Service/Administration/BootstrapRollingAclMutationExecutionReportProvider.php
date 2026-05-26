<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclMutationExecutionReportProviderInterface;
use App\Rolling\Value\Administration\RollingAclMutationExecutionFilter;
use App\Rolling\Value\Administration\RollingAclMutationExecutionReport;
use App\Rolling\Value\Administration\RollingAclMutationExecutionSummary;

/**
 * Bootstrap metadata-only execution report provider.
 *
 * A Doctrine-backed implementation can replace this once Rolling execution
 * events are persisted. Until then, this service exposes a stable contract to
 * Administering without pretending that historical storage exists.
 */
final readonly class BootstrapRollingAclMutationExecutionReportProvider implements RollingAclMutationExecutionReportProviderInterface
{
    public function report(RollingAclMutationExecutionFilter $filter): RollingAclMutationExecutionReport
    {
        return new RollingAclMutationExecutionReport(
            $filter,
            new RollingAclMutationExecutionSummary(0),
            [],
        );
    }
}
