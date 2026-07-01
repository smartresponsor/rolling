<?php

declare(strict_types=1);

namespace App\Rolling\Security\Hmac;

interface NonceStoreInterface
{
    public function seen(string $nonce, int $ttlSec): bool;
}
