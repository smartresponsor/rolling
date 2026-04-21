<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Policy;

use App\Rolling\Policy\V2\DecisionWithObligations;

interface PdpV2Interface
{
    /**
     * @param \App\Rolling\Entity\Role\SubjectId     $subject
     * @param \App\Rolling\Entity\Role\PermissionKey $action
     * @param \App\Rolling\Entity\Role\Scope         $objectScope
     * @param array                                  $context
     *
     * @return DecisionWithObligations
     */
    public function check(\App\Rolling\Entity\Role\SubjectId $subject, \App\Rolling\Entity\Role\PermissionKey $action, \App\Rolling\Entity\Role\Scope $objectScope, array $context = []): DecisionWithObligations;
}
