<?php

declare(strict_types=1);

namespace App\Rolling\Security\Hmac;

interface SecretProviderInterface
{
    /**
     * @param string $keyId
     *
     * @return string|null
     */
    public function secret(string $keyId): ?string;
}
