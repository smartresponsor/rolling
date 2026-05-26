<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Value\Administration\RollingAclMutationExecutionEvent;

/**
 * Records safe Rolling-owned execution events for reviewed ACL mutations.
 */
interface RollingAclMutationExecutionEventRecorderInterface
{
    public function record(RollingAclMutationExecutionEvent $event): void;
}
