<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Tenant;

interface LimitsInterface
{
    /** @return array{max_tuples:int|null,residency:string|null} */
    public function get(string $tenant): array;

    public function set(string $tenant, ?int $maxTuples, ?string $residency): void;
}
