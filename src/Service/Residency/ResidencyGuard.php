<?php

declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Rolling\Service\Residency;

use App\Rolling\ServiceInterface\Residency\ResidencyPolicyInterface;

final class ResidencyGuard
{
    public function __construct(private readonly ResidencyPolicyInterface $pol)
    {
    }

    /**
     * @param array<string, mixed> $attrs
     *
     * @return array{allowed: bool, region: string, headers: list<array{name: string, value: string}>, reason: string}
     */
    public function enforce(string $tenant, array $attrs): array
    {
        $defaultRegion = $this->pol->regionForTenant($tenant);
        $region = (string) ($attrs['region'] ?? $defaultRegion);
        $headers = [['name' => 'X-Data-Region', 'value' => $region]];

        return [
            'allowed' => true,
            'region' => $region,
            'headers' => $headers,
            'reason' => 'ok',
        ];
    }
}
