<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAclMutationApplyRequest;
use App\Rolling\Value\Administration\RollingAclMutationResult;

/**
 * Executes a reviewed ACL apply request through Rolling-owned administration services.
 */
interface RollingAclMutationExecutionGatewayInterface
{
    public function execute(RollingAclMutationApplyRequest $request): RollingAclMutationResult;
}
