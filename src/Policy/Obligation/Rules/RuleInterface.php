<?php

declare(strict_types=1);

namespace App\Rolling\Policy\Obligation\Rules;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Policy\Obligation\Obligation;

interface RuleInterface
{
    public function evaluate(SubjectId $subject, PermissionKey $action, Scope $scope, array $context = []): ?Obligation;
}
