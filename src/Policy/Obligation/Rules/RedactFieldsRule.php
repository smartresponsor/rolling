<?php

declare(strict_types=1);

namespace App\Rolling\Policy\Obligation\Rules;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Policy\Obligation\Obligation;

final class RedactFieldsRule implements RuleInterface
{
    public function __construct(private readonly array $actions = ['*'], private readonly array $fields = [])
    {
    }

    public function evaluate(SubjectId $subject, PermissionKey $action, Scope $scope, array $context = []): ?Obligation
    {
        $value = method_exists($action, 'value') ? $action->value() : (string) $action;
        $matches = in_array('*', $this->actions, true) || in_array($value, $this->actions, true);

        return $matches ? new Obligation('redact_fields', ['fields' => $this->fields]) : null;
    }
}
