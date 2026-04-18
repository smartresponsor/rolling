<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Legacy/Audit/AuditWriter.php
namespace App\Legacy\Audit;
=======
namespace App\Audit\Role;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Audit/Role/AuditWriter.php
/**
 *
 */

/**
 *
 */
interface AuditWriter
{
    /**
     * @param \App\Legacy\Audit\AuditRecord $rec
     * @return void
     */
    public function write(AuditRecord $rec): void;
}
