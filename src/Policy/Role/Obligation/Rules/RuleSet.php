<?php

declare(strict_types=1);

namespace App\Policy\Role\Obligation\Rules;

use Policy\Role\Obligation\Obligations;
use src\Entity\Role\PermissionKey;
use src\Entity\Role\Scope;
use src\Entity\Role\SubjectId;

final class RuleSet
{
    public function __construct(private readonly array $rules) {}

    public function eval(SubjectId $subject, PermissionKey $action, Scope $scope, array $context = []): Obligations
    {
        $obligations = Obligations::empty();

        foreach ($this->rules as $rule) {
            if (!method_exists($rule, 'obligationFor')) {
                continue;
            }

            $obligation = $rule->obligationFor($action, $subject, $scope, $context);
            if ($obligation !== null) {
                $obligations = $obligations->with($obligation);
            }
        }

        return $obligations;
    }
}
