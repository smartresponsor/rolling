<?php
declare(strict_types=1);

namespace App\Attribute\Role\Provider;
/**
 *
 */

/**
 *
 */
final class ResourceProvider implements AttributeProviderInterface
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
     * @return array
     */
    public function forOrg(string $orgId): array
    {
        return [];
    }

    /**
     * @param string $resourceId
     * @return array
     */
    public function forResource(string $resourceId): array
    {
        return ['id' => $resourceId, 'visibility' => 'private'];
    }
}
