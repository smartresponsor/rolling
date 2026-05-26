<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAdministrationSubjectAccessReport;

interface RollingAdministrationSubjectAccessReportProviderInterface
{
    public function reportFor(string $subjectIdentifier, string $scope = 'administering:global'): RollingAdministrationSubjectAccessReport;
}
