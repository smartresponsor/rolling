<?php

declare(strict_types=1);

namespace App\Rolling\Service\Attribute\Provider;

use App\Rolling\ServiceInterface\Attribute\AttributeProviderInterface;

final class OrganizationAttributeProvider implements AttributeProviderInterface
{
    public function forUser(string $userId): array
    {
        return [];
    }

    /**
     * @return string[]
     */
    public function forOrg(string $orgId): array
    {
        return ['id' => $orgId, 'tier' => 'free', 'region' => 'eu'];
    }

    public function forResource(string $resourceId): array
    {
        return [];
    }
}
