<?php
declare(strict_types=1);

namespace Http\Security\Replay;

use PDO;
use Throwable;

/**
 *
 */

/**
 *
 */
final class PdoStore implements StoreInterface
{
    /**
     * @param \PDO $pdo
     * @param string $table
     */
    public function __construct(private readonly PDO $pdo, private readonly string $table = 'replay_nonce')
    {
    }

    /**
     * @param string $nonce
     * @param int $ttlSec
     * @return bool
     */
    public function seen(string $nonce, int $ttlSec): bool
    {
        $now = time();
        $expires = $now + $ttlSec;
        // очистка протухших
        $this->pdo->exec("DELETE FROM {$this->table} WHERE expires_ts < {$now}");
        // попытка вставки
        $stmt = $this->pdo->prepare("INSERT INTO {$this->table} (nonce, expires_ts) VALUES (:n, :e)");
        try {
            $stmt->execute([':n' => $nonce, ':e' => $expires]);
            return true;
        } catch (Throwable $e) {
            return false; // duplicate => replay
        }
    }
}
