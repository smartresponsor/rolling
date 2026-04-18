<?php

declare(strict_types=1);

namespace App\Policy\Role\Obligation\Applier;

use Policy\Role\Obligation\Obligations;

final class ArrayApplier
{
    public function apply(array $data, Obligations $obligations): array
    {
        $headers = [];

        foreach ($obligations->all() as $obligation) {
            if ($obligation->type === 'redact_fields') {
                foreach ((array) ($obligation->params['fields'] ?? []) as $field) {
                    if (array_key_exists($field, $data)) {
                        $data[$field] = '***';
                    }
                }
            }

            if ($obligation->type === 'watermark') {
                $headers[(string) ($obligation->params['header'] ?? 'X-Policy')] = (string) ($obligation->params['value'] ?? '');
            }
        }

        return [
            'data' => $data,
            'headers' => $headers,
        ];
    }
}
