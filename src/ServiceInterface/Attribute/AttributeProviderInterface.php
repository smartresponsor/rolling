<?php

declare(strict_types=1);

<<<<<<< HEAD:src/ServiceInterface/Attribute/AttributeProviderInterface.php
namespace App\ServiceInterface\Attribute;
=======
namespace App\Attribute\Role\Provider;

>>>>>>> 386b7f1226aea2a36c67528b73ac2cb63b6bedfa:src/Attribute/Role/Provider/AttributeProviderInterface.php
/**
 *
 */

/**
 *
 */
interface AttributeProviderInterface
{
    /** @return array<string,mixed> */
    public function forUser(string $userId): array;

    /** @return array<string,mixed> */
    public function forOrg(string $orgId): array;

    /** @return array<string,mixed> */
    public function forResource(string $resourceId): array;
}
