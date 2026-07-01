<?php

declare(strict_types=1);

namespace App\Rolling\Security\Hmac;

final class InMemoryNonceStore implements NonceStoreInterface
{
    /**
     * @var array<string, int>
     */
    private array $exp = [];

    public function seen(string $nonce, int $ttlSec): bool
    {
        $now = time();
        foreach ($this->exp as $n => $e) {
            if ($e < $now) {
                unset($this->exp[$n]);
            }
        }
        if ('' === $nonce) {
            return false;
        }
        if (isset($this->exp[$nonce]) && $this->exp[$nonce] >= $now) {
            return true;
        }
        $this->exp[$nonce] = $now + $ttlSec;

        return false;
    }
}
