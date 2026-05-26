<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAclStorageReadinessReport;

interface RollingAclStorageReadinessReportProviderInterface
{
    public function report(): RollingAclStorageReadinessReport;
}
