<?php

declare(strict_types=1);

namespace App\Rolling\Policy\Role\Obligation\Applier;

use App\Rolling\Policy\Obligation\Obligations;

final class ArrayApplier
{
    /** @param array<string,mixed> $data @return array{data:array<string,mixed>,headers:array<string,string>} */
    public function apply(array $data, Obligations $obligations): array
    {
        $headers = [];

        foreach ($obligations->all() as $obligation) {
            if ('redact_fields' === $obligation->type) {
                foreach ((array) ($obligation->params['fields'] ?? []) as $field) {
                    if (is_string($field) && array_key_exists($field, $data)) {
                        $data[$field] = '***';
                    }
                }
            }

            if ('watermark' === $obligation->type) {
                $headers[(string) ($obligation->params['header'] ?? 'X-Policy')] = (string) ($obligation->params['value'] ?? '');
            }
        }

        return ['data' => $data, 'headers' => $headers];
    }
}
