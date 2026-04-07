<?php

declare(strict_types=1);

namespace App\InfrastructureInterface\Security;

interface ReplayNonceStoreInterface
{
    public function seen(string $nonce, int $ttlSec): bool;
}
