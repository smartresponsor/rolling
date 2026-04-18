<?php

declare(strict_types=1);

namespace App\Policy\Role\Obligation\Rules;

use App\Entity\Role\PermissionKey;
use App\Policy\Obligation\Obligation;

final class RedactFieldsRule
{
    /** @param list<string> $actions @param list<string> $fields */
    public function __construct(private readonly array $actions, private readonly array $fields)
    {
    }

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
