<?php

declare(strict_types=1);

namespace App\Security\Admin;

final class Roles
{
    public const string OWNER = 'owner';
    public const string OPERATOR = 'operator';
    public const string AUDITOR = 'auditor';

    /** @return list<string> */
    public static function allowed(): array
    {
        return [self::OWNER, self::OPERATOR, self::AUDITOR];
    }
}
