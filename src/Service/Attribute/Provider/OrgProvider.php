<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Service/Attribute/Provider/OrgProvider.php
namespace App\Service\Attribute\Provider;
=======
namespace App\Attribute\Role\Provider;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Attribute/Role/Provider/OrgProvider.php
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
