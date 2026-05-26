<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclMutationExecutionEventRecorderInterface;
use App\Rolling\Value\Administration\RollingAclMutationExecutionEvent;

/**
 * Safe default recorder until Doctrine-backed Rolling execution events are introduced.
 */
final class NullRollingAclMutationExecutionEventRecorder implements RollingAclMutationExecutionEventRecorderInterface
{
    public function record(RollingAclMutationExecutionEvent $event): void
    {
        // Intentionally empty. Wave 16 stabilizes the event boundary first.
    }
}
