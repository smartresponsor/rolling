<?php

declare(strict_types=1);

namespace App\Policy\Obligation\Rules;

use App\Policy\Obligation\Obligation;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;

interface RuleInterface
{
    public function evaluate(SubjectId $subject, PermissionKey $action, Scope $scope, array $context = []): ?Obligation;
}
