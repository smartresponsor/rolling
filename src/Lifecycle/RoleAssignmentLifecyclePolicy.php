<?php

declare(strict_types=1);

namespace App\Rolling\Lifecycle;

/**
 * Guards allowed lifecycle transitions for role assignment.
 *
 * This policy is intentionally string-based for now so it can wrap existing
 * entity status fields without forcing a schema migration. Core status enums
 * can be introduced per component once runtime metadata validation is green.
 */
final class RoleAssignmentLifecyclePolicy
{
    /** @var array<string, list<string>> */
    private const TRANSITIONS = [
        'proposed' => ['active', 'rejected'],
        'active' => ['suspended', 'revoked', 'expired'],
        'suspended' => ['active', 'revoked', 'expired'],
        'rejected' => [],
        'revoked' => [],
        'expired' => [],
    ];

    public static function canTransition(string $from, string $to): bool
    {
        if ($from === $to) {
            return true;
        }

        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }

    public static function assertCanTransition(string $from, string $to): void
    {
        if (!self::canTransition($from, $to)) {
            throw new \DomainException(sprintf('Invalid Rolling lifecycle transition from "%s" to "%s".', $from, $to));
        }
    }

    /** @return list<string> */
    public static function allowedTargets(string $from): array
    {
        return self::TRANSITIONS[$from] ?? [];
    }
}
