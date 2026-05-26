<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAclHealthReport;

/**
 * Provides a safe health report for Rolling ACL administration consumers.
 */
interface RollingAclHealthReportProviderInterface
{
    public function report(): RollingAclHealthReport;
}
