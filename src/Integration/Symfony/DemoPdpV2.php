<?php

declare(strict_types=1);

namespace App\Rolling\Integration\Symfony;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Policy\Obligation\Obligations;
use App\Rolling\Policy\V2\DecisionWithObligations;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;

final class DemoPdpV2 implements PdpV2Interface
{
    public function check(SubjectId $subject, PermissionKey $action, Scope $objectScope, array $context = []): DecisionWithObligations
    {
        if ('' === $subject->value() || '' === $action->value()) {
            return DecisionWithObligations::deny('invalid_request', Obligations::empty());
        }

        return DecisionWithObligations::allow('local_demo', Obligations::empty());
    }
}
