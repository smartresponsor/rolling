<?php
declare(strict_types=1);

namespace App\Legacy\Policy\Registry;

/**
 *
 */

/**
 *
 */
interface SourceInterface
{
    /** @return array<string,mixed> */
    public function get(): array;
}
