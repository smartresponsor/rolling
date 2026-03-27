<?php

declare(strict_types=1);

namespace App\Audit\Role\Export;

use App\Audit\Role\AuditRecord;

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
