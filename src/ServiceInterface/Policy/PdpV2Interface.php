<?php

declare(strict_types=1);

namespace App\ServiceInterface\Policy;

use App\Policy\V2\DecisionWithObligations;

/**
 *
 */

/**
 *
 */
interface PdpV2Interface
{
    /**
     * @param \App\Entity\Role\SubjectId $subject
     * @param \App\Entity\Role\PermissionKey $action
     * @param \App\Entity\Role\Scope $objectScope
     * @param array $context
     * @return \App\Policy\V2\DecisionWithObligations
     */
    public function check(\App\Entity\Role\SubjectId $subject, \App\Entity\Role\PermissionKey $action, \App\Entity\Role\Scope $objectScope, array $context = []): DecisionWithObligations;
}
