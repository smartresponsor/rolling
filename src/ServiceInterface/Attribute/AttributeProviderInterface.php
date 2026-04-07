<?php
declare(strict_types=1);

namespace App\ServiceInterface\Attribute;
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
