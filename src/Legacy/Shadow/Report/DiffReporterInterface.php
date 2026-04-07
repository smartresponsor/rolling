<?php
declare(strict_types=1);

namespace App\Legacy\Shadow\Report;
/**
 *
 */

/**
 *
 */
interface DiffReporterInterface
{

    /**
     * @param array $payload
     */
    public function report(array $payload): void;
}
