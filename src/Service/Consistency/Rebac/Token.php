<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Service/Consistency/Rebac/Token.php
namespace App\Service\Consistency\Rebac;
=======
namespace App\Consistency\Role\Policy;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Consistency/Role/Policy/Token.php
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
