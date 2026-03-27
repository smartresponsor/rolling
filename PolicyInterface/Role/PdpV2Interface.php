<?php

declare(strict_types=1);

namespace PolicyInterface\Role;

use Policy\Role\V2\DecisionWithObligations;

/**
 *
 */

/**
 *
 */
interface PdpV2Interface
{
    /**
     * @param \src\Entity\Role\SubjectId $subject
     * @param \src\Entity\Role\PermissionKey $action
     * @param \src\Entity\Role\Scope $objectScope
     * @param array $context
     * @return \Policy\Role\V2\DecisionWithObligations
     */
    public function check(\src\Entity\Role\SubjectId $subject, \src\Entity\Role\PermissionKey $action, \src\Entity\Role\Scope $objectScope, array $context = []): DecisionWithObligations;
}
