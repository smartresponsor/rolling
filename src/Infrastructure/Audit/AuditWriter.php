<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Audit;

interface AuditWriter
{
    /**
     * @param AuditRecord $rec
     *
     * @return void
     */
    public function write(AuditRecord $rec): void;
}
