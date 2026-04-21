<?php

declare(strict_types=1);

namespace App\Rolling\Service\Sod;

final class SodGuard
{
    public function validate(array $attrs): array
    {
        $requester = (string) ($attrs['requester'] ?? '');
        $approver = (string) ($attrs['approver'] ?? '');

        if ('' !== $requester && '' !== $approver && $requester === $approver) {
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
