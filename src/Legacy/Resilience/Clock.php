<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Legacy/Resilience/Clock.php
namespace App\Legacy\Resilience;
=======
namespace App\Resilience\Role;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Resilience/Role/Clock.php
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
