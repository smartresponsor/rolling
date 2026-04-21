<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Tenant;

interface QuotaInterface
{
    /** @return array{limit_per_min:int} */
    public function getLimit(string $tenant): array;

    public function setLimit(string $tenant, int $perMin): void;

    /** @return array{allowed:bool, remaining:int, reset:int} */
    public function consume(string $tenant, int $cost = 1): array;
}
