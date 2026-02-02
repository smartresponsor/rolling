<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Service\Role\Mask;

use src\ServiceInterface\Role\Mask\DataMaskerInterface;

/**
 *
 */

/**
 *
 */
final class DataMasker implements DataMaskerInterface
{
    /**
     * @param array $data
     * @param array $rules
     * @return array
     */
    public function mask(array $data, array $rules): array
    {
        foreach ($rules as $field => $rule) {
            if (!array_key_exists($field, $data)) continue;
            $v = $data[$field];
            switch ($rule) {
                case 'redact':
                    $data[$field] = '***';
                    break;
                case 'hash':
                    $data[$field] = hash('sha256', (string)$v);
                    break;
                case 'last4':
                    $s = (string)$v;
                    $data[$field] = (strlen($s) > 4) ? str_repeat('*', len: max(0, strlen($s) - 4)) . substr($s, -4) : $s;
                    break;
                case 'remove':
                    unset($data[$field]);
                    break;
                default:
                    // no-op
                    break;
            }
        }
        return $data;
    }
}
