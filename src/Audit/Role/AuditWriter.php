<?php
declare(strict_types=1);

namespace App\Audit\Role;
/**
 *
 */

/**
 *
 */
interface AuditWriter
{
    /**
     * @param \App\Audit\Role\AuditRecord $rec
     * @return void
     */
    public function write(AuditRecord $rec): void;
}
