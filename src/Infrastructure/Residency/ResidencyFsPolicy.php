<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Residency;

use App\Rolling\ServiceInterface\Residency\ResidencyPolicyInterface;

final class ResidencyFsPolicy implements ResidencyPolicyInterface
{
    public function __construct(private readonly string $file)
    {
    }

    public function regionForTenant(string $tenant): string
    {
        $config = $this->loadConfig($tenant);

        return (string) ($config['defaultRegion'] ?? 'us');
    }

    /** @return array{allowedRegions:list<string>,defaultRegion:string} */
    private function loadConfig(string $tenant): array
    {
        if (!is_file($this->file)) {
            return ['allowedRegions' => [], 'defaultRegion' => 'us'];
        }

        $decoded = json_decode((string) file_get_contents($this->file), true);
        $config = is_array($decoded) ? $decoded : [];
        $tenantConfig = $config[$tenant] ?? ['allowedRegions' => [], 'defaultRegion' => 'us'];

        return [
            'allowedRegions' => array_values(array_map('strval', (array) ($tenantConfig['allowedRegions'] ?? []))),
            'defaultRegion' => (string) ($tenantConfig['defaultRegion'] ?? 'us'),
        ];
    }
}
