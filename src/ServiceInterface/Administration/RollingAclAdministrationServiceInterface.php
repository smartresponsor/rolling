<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAclMutationRequest;
use App\Rolling\Value\Administration\RollingAclMutationResult;

interface RollingAclAdministrationServiceInterface
{
    public function mutate(RollingAclMutationRequest $request): RollingAclMutationResult;
}
