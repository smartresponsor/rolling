<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclMutationAuditRecorderInterface;
use App\Rolling\Value\Administration\RollingAclMutationAuditEvent;

/**
 * Default safe no-op recorder until a Doctrine-backed audit sink is configured.
 */
final class NullRollingAclMutationAuditRecorder implements RollingAclMutationAuditRecorderInterface
{
    public function record(RollingAclMutationAuditEvent $event): void
    {
        // Intentionally no-op. Host applications may replace this with system storage.
    }
}
