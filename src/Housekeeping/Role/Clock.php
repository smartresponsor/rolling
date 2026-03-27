<?php

declare(strict_types=1);

namespace App\Housekeeping\Role;

/**
 *
 */

/**
 *
 */
final class Clock
{
    /**
     * @return int
     */
    public static function now(): int
    {
        return time();
    }
}
