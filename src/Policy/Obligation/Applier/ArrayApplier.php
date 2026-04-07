<?php

declare(strict_types=1);

namespace App\Policy\Obligation\Applier;

use App\Policy\Obligation\Obligations;

final class ArrayApplier
{
    public function apply(array $data, Obligations $obligations): array
    {
        foreach ($obligations->all() as $obligation) {
            if ($obligation->type === 'redact_fields') {
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
