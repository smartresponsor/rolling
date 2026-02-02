<?php
declare(strict_types=1);

namespace Policy\Role\Registry;

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
