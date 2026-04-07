<?php
declare(strict_types=1);

namespace App\Infrastructure\Audit;

use App\Policy\Obligation\Obligations;

/**
 *
 */

/**
 *
 */
final class ObligationSummary
{
    /** @return array<string,mixed> */
    public static function summarize(Obligations $obl): array
    {
        $types = [];
        foreach ($obl->all() as $o) {
            $types[] = $o->type;
        }
        return ['types' => array_values(array_unique($types)), 'count' => count($types)];
    }
}
