<?php

declare(strict_types=1);

namespace App\Infrastructure\Security\Replay;

use App\InfrastructureInterface\Security\ReplayNonceStoreInterface;
use PDO;
use Throwable;

final class PdoReplayNonceStore implements ReplayNonceStoreInterface
{
    public function __construct(
        private readonly PDO $pdo,
        private readonly string $table = 'replay_nonce',
    ) {
    }

    public function seen(string $nonce, int $ttlSec): bool
    {
        $now = time();
        $expires = $now + $ttlSec;

        $this->pdo->exec(sprintf('DELETE FROM %s WHERE expires_ts < %d', $this->table, $now));
        $stmt = $this->pdo->prepare(sprintf('INSERT INTO %s (nonce, expires_ts) VALUES (:n, :e)', $this->table));

        try {
            $stmt->execute([':n' => $nonce, ':e' => $expires]);

            return true;
        } catch (Throwable) {
            return false;
        }
    }
}
