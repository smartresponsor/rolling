<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingFieldAccessDecision;
use App\Rolling\Value\Administration\RollingFieldAccessDecisionRequest;

interface RollingFieldAccessDecisionServiceInterface
{
    public function decide(RollingFieldAccessDecisionRequest $request): RollingFieldAccessDecision;
}
