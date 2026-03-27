<?php

declare(strict_types=1);

namespace App\Net\Role\Opa;

/**
 *
 */

/**
 *
 */
interface OpaClientInterface
{
    /**
     * @param string $dataPath
     * @param array $input
     * @return array<string,mixed> result payload from OPA ("result": ...)
     */
    public function evaluate(string $dataPath, array $input): array;
}
