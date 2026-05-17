<?php

declare(strict_types=1);

namespace App\Rolling\Security\Hmac;

interface NonceStoreInterface
{
    /**
     * @param string $nonce
     * @param int    $ttlSec
     *
     * @return bool
     */
    public function seen(string $nonce, int $ttlSec): bool;
}
