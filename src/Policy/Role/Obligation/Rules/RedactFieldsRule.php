<?php

declare(strict_types=1);

namespace App\Policy\Role\Obligation\Rules;

use Policy\Role\Obligation\Obligation;
use src\Entity\Role\PermissionKey;

final class RedactFieldsRule
{
    public function __construct(private readonly array $actions, private readonly array $fields) {}

    public function obligationFor(PermissionKey $action): ?Obligation
    {
        foreach ($this->actions as $pattern) {
            if ($pattern === '*' || $pattern === $action->value()) {
                return new Obligation('redact_fields', ['fields' => $this->fields]);
            }
        }

        return null;
    }
}
