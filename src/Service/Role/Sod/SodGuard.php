<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace Sod;

/**
 *
 */

/**
 *
 */
final class SodGuard
{
    /**
     * @param array $attrs
     * @return array
     */
    public function validate(array $attrs): array
    {
        $req = (string) ($attrs['requester'] ?? '');
        $app = (string) ($attrs['approver'] ?? '');
        if ($req !== '' && $app !== '' && $req === $app) {
            return ['ok' => false, 'reason' => 'sod-same-person'];
        }
        $need = (int) ($attrs['approverNeed'] ?? 1);
        $have = (int) ($attrs['approverHave'] ?? 1);
        if ($have < $need) {
            return ['ok' => false, 'reason' => 'cardinality-low'];
        }
        return ['ok' => true, 'reason' => 'ok'];
    }
}
