<?php

declare(strict_types=1);

namespace App\Policy\Role\Obligation\Rules;

use App\Entity\Role\PermissionKey;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;
use App\Policy\Obligation\Obligations;

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
            if ($obligation !== null) {
                $obligations = $obligations->with($obligation);
            }
        }

        return $obligations;
    }
}
