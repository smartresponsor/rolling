<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Legacy/Housekeeping/Clock.php
namespace App\Legacy\Housekeeping;
=======
namespace App\Housekeeping\Role;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Housekeeping/Role/Clock.php
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
