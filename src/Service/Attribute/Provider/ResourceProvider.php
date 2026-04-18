<?php

declare(strict_types=1);

<<<<<<< HEAD:src/Service/Attribute/Provider/ResourceProvider.php
namespace App\Service\Attribute\Provider;
=======
namespace App\Attribute\Role\Provider;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Attribute/Role/Provider/ResourceProvider.php
/**
 *
 */

/**
 *
 */
use App\ServiceInterface\Attribute\AttributeProviderInterface;

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
