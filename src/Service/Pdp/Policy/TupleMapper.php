<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Pdp\Policy;

use App\Rolling\Infrastructure\Rebac\Tuple;

/**
 * Map internal grant rules to ReBAC tuples.
 * Supported rule keys: subjectRole, subjectId, action->relation, resourceType, tenant(optional).
 */
final class TupleMapper
{
    /**
     * @param array $grants list of grant records
     *
     * @return Tuple[]
     */
    public static function toTuples(array $grants): array
    {
        $out = [];
        foreach ($grants as $g) {
            $userType = isset($g['subjectId']) ? 'user' : 'role';
            $userId = $g['subjectId'] ?? ($g['subjectRole'] ?? 'unknown');
            $relation = $g['action'] ?? 'can_read';
            $objectType = $g['resourceType'] ?? 'object';
            $objectId = $g['resourceId'] ?? '*';
            $tenant = $g['tenant'] ?? null;
            $out[] = new Tuple($userType, (string) $userId, (string) $relation, (string) $objectType, (string) $objectId, $tenant ? (string) $tenant : null);
        }

        return $out;
    }
}
