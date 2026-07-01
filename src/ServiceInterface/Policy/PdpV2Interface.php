<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Policy;

use App\Rolling\Policy\V2\DecisionWithObligations;

interface PdpV2Interface
{
    public function check(\App\Rolling\Entity\Role\SubjectId $subject, \App\Rolling\Entity\Role\PermissionKey $action, \App\Rolling\Entity\Role\Scope $objectScope, array $context = []): DecisionWithObligations;
}
