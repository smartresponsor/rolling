<?php

declare(strict_types=1);

namespace App\Rolling\Security\Hmac;

final class InMemorySecretProvider implements SecretProviderInterface
{
    /**
     * @param array<string, string> $map
     */
    public function __construct(private readonly array $map)
    {
    }

    /**
     * @param string $keyId
     *
     * @return string|null
     */
    public function secret(string $keyId): ?string
    {
        return $this->map[$keyId] ?? null;
    }
}
