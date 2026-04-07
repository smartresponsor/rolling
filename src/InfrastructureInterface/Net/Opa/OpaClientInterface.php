<?php
declare(strict_types=1);

namespace App\InfrastructureInterface\Net\Opa;

interface OpaClientInterface
{
    /**
     * @param array $input
     * @return array<string,mixed>
     */
    public function evaluate(string $dataPath, array $input): array;
}
