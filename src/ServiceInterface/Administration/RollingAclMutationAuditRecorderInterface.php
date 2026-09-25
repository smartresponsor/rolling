<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

use App\Rolling\Event\RollingAclMutationAuditEvent;

/**
 * Records safe metadata about ACL administration mutations.
 */
interface RollingAclMutationAuditRecorderInterface
{
    public function record(RollingAclMutationAuditEvent $event): void;
}
