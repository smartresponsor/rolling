<?php

declare(strict_types=1);

namespace App\Rolling\Policy\Role\Obligation\Rules;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Policy\Obligation\Obligations;

final class RuleSet
{
    /** @param list<object> $rules */
    public function __construct(private readonly array $rules)
    {
    }

    /** @param array<string,mixed> $context */
    public function eval(SubjectId $subject, PermissionKey $action, Scope $scope, array $context = []): Obligations
    {
        $obligations = Obligations::empty();

        foreach ($this->rules as $rule) {
            if (!method_exists($rule, 'obligationFor')) {
                continue;
            }

            $obligation = $rule->obligationFor($action, $subject, $scope, $context);
            if (null !== $obligation) {
                $obligations = $obligations->with($obligation);
            }
        }

        return $obligations;
    }
}
