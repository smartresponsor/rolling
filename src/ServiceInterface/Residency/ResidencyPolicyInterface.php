<?php

declare(strict_types=1);

namespace App\ServiceInterface\Residency;

interface ResidencyPolicyInterface
{
    public function regionForTenant(string $tenant): string;
}
