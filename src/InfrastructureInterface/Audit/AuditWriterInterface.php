<?php

declare(strict_types=1);

namespace App\Rolling\InfrastructureInterface\Audit;

use App\Rolling\Infrastructure\Audit\AuditRecord;

interface AuditWriterInterface
{
    public function write(AuditRecord $rec): void;
}
