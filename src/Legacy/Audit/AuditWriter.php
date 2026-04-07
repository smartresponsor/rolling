<?php
declare(strict_types=1);

namespace App\Legacy\Audit;
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
