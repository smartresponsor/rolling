<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Service\Residency;

use App\Rolling\ServiceInterface\Residency\ResidencyPolicyInterface;

/**
 * Simple map-based residency policy.
 */
final class StaticResidencyPolicy implements ResidencyPolicyInterface
{
    public function __construct(private readonly array $map = ['t1' => 'us', 't2' => 'eu'], private readonly string $fallback = 'us')
    {
    }

    public function regionForTenant(string $tenant): string
    {
        return $this->map[$tenant] ?? $this->fallback;
    }
}
