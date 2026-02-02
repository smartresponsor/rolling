<?php
declare(strict_types=1);

namespace Http\Security\Replay;

/** Store для защиты от повторов. */
interface StoreInterface
{
    /** Сохранить nonce с TTL. Возвращает true, если nonce видим впервые. */
    public function seen(string $nonce, int $ttlSec): bool;
}
