<?php
declare(strict_types=1);

namespace App\Attribute\Role\Provider;
/**
 *
 */

/**
 *
 */
final class UserProvider implements AttributeProviderInterface
{
    /**
     * @param string $userId
     * @return array
     */
    public function forUser(string $userId): array
    {
        return ['id' => $userId, 'roles' => ['viewer']];
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
        return [];
    }
}
