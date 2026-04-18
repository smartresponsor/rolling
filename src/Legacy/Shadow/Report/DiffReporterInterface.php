<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Legacy/Shadow/Report/DiffReporterInterface.php
namespace App\Legacy\Shadow\Report;
=======
namespace App\Shadow\Role\Report;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Shadow/Role/Report/DiffReporterInterface.php
/**
 *
 */

/**
 *
 */
interface DiffReporterInterface
{
    /**
     * @param array $payload
     */
    public function report(array $payload): void;
}
