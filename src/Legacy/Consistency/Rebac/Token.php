<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Legacy/Consistency/Rebac/Token.php
namespace App\Legacy\Consistency\Rebac;
=======
namespace App\Consistency\Role\Rebac;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Consistency/Role/Rebac/Token.php
/**
 *
 */

/**
 *
 */
final class Token
{
    /**
     * @param int $rev
     */
    public function __construct(public int $rev) {}

    /**
     * @return string
     */
    public function __toString(): string
    {
        return (string) $this->rev;
    }
}
