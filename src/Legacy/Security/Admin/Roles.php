<?php

declare(strict_types=1);

namespace App\Legacy\Security\Admin;

/**
 *
 */

/**
 *
 */
final class Roles
{
    public const OWNER = 'owner';
    public const OPERATOR = 'operator';
    public const AUDITOR = 'auditor';

    /** @return list<string> */
    public static function allowed(): array
    {
        return [self::OWNER, self::OPERATOR, self::AUDITOR];
    }
}
