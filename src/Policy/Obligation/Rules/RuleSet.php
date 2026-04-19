<?php

declare(strict_types=1);

namespace App\Policy\Obligation\Rules;

use App\Entity\Role\PermissionKey;
use App\Entity\Role\Scope;
use App\Entity\Role\SubjectId;
use App\Policy\Obligation\Obligation;
use App\Policy\Obligation\Obligations;

final class RuleSet
{
    /** @param list<object> $rules */
    public function __construct(private readonly array $rules = [])
    {
    }

    public function eval(SubjectId $subject, PermissionKey $action, Scope $scope, array $context = []): Obligations
    {
        $out = Obligations::empty();
        foreach ($this->rules as $rule) {
            if (!$rule instanceof RuleInterface) {
                continue;
            }
            $obligation = $rule->evaluate($subject, $action, $scope, $context);
            if ($obligation instanceof Obligation) {
                $out->add($obligation);
            }
        }

        return $out;
    }
}
