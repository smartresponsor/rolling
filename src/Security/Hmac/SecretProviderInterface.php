<?php

declare(strict_types=1);

namespace App\Rolling\Security\Hmac;

interface SecretProviderInterface
{
    public function secret(string $keyId): ?string;
}
