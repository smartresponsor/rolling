<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAclMutationExecutionFilter;
use App\Rolling\Value\Administration\RollingAclMutationExecutionReport;

/**
 * Provides metadata-only reports for ACL mutation execution events.
 */
interface RollingAclMutationExecutionReportProviderInterface
{
    public function report(RollingAclMutationExecutionFilter $filter): RollingAclMutationExecutionReport;
}
