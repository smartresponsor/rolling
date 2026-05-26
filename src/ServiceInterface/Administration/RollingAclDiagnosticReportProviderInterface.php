<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAclDiagnosticReport;

interface RollingAclDiagnosticReportProviderInterface
{
    public function report(): RollingAclDiagnosticReport;
}
