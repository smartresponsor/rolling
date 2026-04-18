<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Policy/Obligation/Obligation.php
namespace App\Policy\Obligation;
=======
namespace Policy\Role\Obligation;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:Policy/Role/Obligation/Obligation.php
/**
 *
 */

/**
 *
 */
final class Obligation
{
    /**
     * @param string $type
     * @param array $params
     */
    public function __construct(public string $type, public array $params = []) {}
}
