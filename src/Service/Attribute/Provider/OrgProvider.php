<?php

declare(strict_types=1);

namespace App\Service\Attribute\Provider;
/**
 *
 */

/**
 *
 */
use App\ServiceInterface\Attribute\AttributeProviderInterface;

final class OrgProvider implements AttributeProviderInterface
{
    /**
     * @param string $userId
     * @return array
     */
    public function forUser(string $userId): array
    {
        return [];
    }

    /**
     * @param string $orgId
     * @return string[]
     */
    public function forOrg(string $orgId): array
    {
        return ['id' => $orgId, 'tier' => 'free', 'region' => 'eu'];
    }

    /**
     * @param string $resourceId
     * @return array
     */
    public function forResource(string $resourceId): array
    {
        return [];
    }
}
