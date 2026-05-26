<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAclMutationPlan;
use App\Rolling\Value\Administration\RollingAclMutationRequest;

interface RollingAclMutationPlannerInterface
{
    public function plan(RollingAclMutationRequest $request): RollingAclMutationPlan;
}
