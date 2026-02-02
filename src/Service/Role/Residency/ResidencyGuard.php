<?php
declare(strict_types=1);
/* Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp */

namespace App\Service\Role\Residency;

use App\Domain\Role\Port\ResidencyPolicyPort;

/**
 *
 */

/**
 *
 */
final class ResidencyGuard
{
    /**
     * @param \App\Domain\Role\Port\ResidencyPolicyPort $pol
     */
    public function __construct(private readonly ResidencyPolicyPort $pol)
    {
    }

    /**
     * @param string $tenant
     * @param array $attrs
     * @return array
     */
    public function enforce(string $tenant, array $attrs): array
    {
        $cfg = $this->pol->load($tenant);
        $allowed = (array)($cfg['allowedRegions'] ?? []);
        $def = (string)($cfg['defaultRegion'] ?? 'us');
        $region = (string)($attrs['region'] ?? $def);
        $ok = empty($allowed) || in_array($region, $allowed, true);
        $headers = [['name' => 'X-Data-Region', 'value' => $region]];
        $reason = $ok ? 'ok' : 'region-not-allowed';
        return ['allowed' => $ok, 'region' => $region, 'headers' => $headers, 'reason' => $reason];
    }
}
