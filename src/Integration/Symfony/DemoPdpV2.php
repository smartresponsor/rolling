<?php

declare(strict_types=1);

namespace App\Integration\Symfony;

use App\Entity\Role\PermissionKey;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;
use App\Policy\Obligation\Obligations;
use App\Policy\V2\DecisionWithObligations;
use App\ServiceInterface\Policy\PdpV2Interface;

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
