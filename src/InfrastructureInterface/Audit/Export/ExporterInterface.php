<?php
declare(strict_types=1);

namespace App\InfrastructureInterface\Audit\Export;

use App\Infrastructure\Audit\AuditRecord;

/**
 *
 */

/**
 *
 */
interface ExporterInterface
{
    /**
     * @param iterable<AuditRecord> $records
     * @param string $path
     */
    public function export(iterable $records, string $path): void;
}
