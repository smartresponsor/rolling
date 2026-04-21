<?php

declare(strict_types=1);

namespace App\Rolling\InfrastructureInterface\Audit\Export;

use App\Rolling\Infrastructure\Audit\AuditRecord;

interface ExporterInterface
{
    /**
     * @param iterable<AuditRecord> $records
     * @param string                $path
     */
    public function export(iterable $records, string $path): void;
}
