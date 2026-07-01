<?php

declare(strict_types=1);

namespace App\Rolling\Service\Attribute\Provider;

use App\Rolling\ServiceInterface\Attribute\AttributeProviderInterface;

final class UserAttributeProvider implements AttributeProviderInterface
{
    public function forUser(string $userId): array
    {
        return ['id' => $userId, 'roles' => ['viewer']];
    }

    public function forOrg(string $orgId): array
    {
        return [];
    }

    public function forResource(string $resourceId): array
    {
        return [];
    }
}
