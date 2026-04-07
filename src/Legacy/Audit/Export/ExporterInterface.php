<?php
declare(strict_types=1);

namespace App\Legacy\Audit\Export;

use App\Legacy\Audit\AuditRecord;

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
