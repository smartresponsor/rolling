<?php

declare(strict_types=1);

namespace App\Integration\Symfony;

use Policy\Role\Obligation\Obligations;
use Policy\Role\V2\DecisionWithObligations;
use PolicyInterface\Role\PdpV2Interface;
use src\Entity\Role\PermissionKey;
use src\Entity\Role\Scope;
use src\Entity\Role\SubjectId;

final class DemoPdpV2 implements PdpV2Interface
{
    public function check(SubjectId $subject, PermissionKey $action, Scope $objectScope, array $context = []): DecisionWithObligations
    {
        if ($subject->value() === '' || $action->value() === '') {
            return DecisionWithObligations::deny('invalid_request', Obligations::empty());
        }

        return DecisionWithObligations::allow('local_demo', Obligations::empty());
    }
}
