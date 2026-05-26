<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAclRemediationPlan;

interface RollingAclRemediationPlanProviderInterface
{
    public function plan(): RollingAclRemediationPlan;
}
