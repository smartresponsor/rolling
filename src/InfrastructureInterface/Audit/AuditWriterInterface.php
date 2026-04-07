<?php
declare(strict_types=1);

namespace App\InfrastructureInterface\Audit;

use App\Infrastructure\Audit\AuditRecord;

interface AuditWriterInterface
{
    public function write(AuditRecord $rec): void;
}
