<?php

declare(strict_types=1);

namespace App\Rolling\Policy\Obligation\Applier;

use App\Rolling\Policy\Obligation\Obligations;

final class ArrayApplier
{
    public function apply(array $data, Obligations $obligations): array
    {
        foreach ($obligations->all() as $obligation) {
            if ('redact_fields' === $obligation->type) {
                foreach ((array) ($obligation->params['fields'] ?? []) as $field) {
                    if (array_key_exists($field, $data)) {
                        $data[$field] = '***';
                    }
                }
            }
        }

        return ['data' => $data];
    }
}
