<?php
declare(strict_types=1);

namespace App\Resilience\Role;
/**
 *
 */

/**
 *
 */
interface Clock
{
    /**
     * @return int
     */
    public function now(): int;
}

/**
 *
 */

/**
 *
 */
final class SystemClock implements Clock
{
    /**
     * @return int
     */
    public function now(): int
    {
        return time();
    }
}
