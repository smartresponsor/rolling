<?php

declare(strict_types=1);

namespace App\Service\Mask;

use App\ServiceInterface\Mask\DataMaskerInterface;

final class DataMasker implements DataMaskerInterface
{
    public function mask(array $data, array $rules): array
    {
        foreach ($rules as $field => $rule) {
            if (!array_key_exists($field, $data)) {
                continue;
            }

            $value = $data[$field];

            switch ($rule) {
                case 'redact':
                    $data[$field] = '***';
                    break;
                case 'hash':
                    $data[$field] = hash('sha256', (string) $value);
                    break;
                case 'last4':
                    $string = (string) $value;
                    $data[$field] = strlen($string) > 4
                        ? str_repeat('*', max(0, strlen($string) - 4)) . substr($string, -4)
                        : $string;
                    break;
                case 'remove':
                    unset($data[$field]);
                    break;
                default:
                    break;
            }
        }

        return $data;
    }
}
