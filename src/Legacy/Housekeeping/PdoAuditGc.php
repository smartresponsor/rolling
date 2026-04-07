<?php
declare(strict_types=1);

namespace App\Legacy\Housekeeping;

use PDO;

/**
 * Удаляет записи из role_audit старше порога. Работает батчами и для SQLite, и для PostgreSQL.
 * Для Postgres колонка ts — TIMESTAMPTZ, поэтому используем to_timestamp(:ts).
 */
final class PdoAuditGc
{
    /**
     * @param \PDO $pdo
     * @param string $table
     */
    public function __construct(private readonly PDO $pdo, private readonly string $table = 'role_audit')
    {
    }

    /**
     * @return int кол-во удалённых строк
     */
    public function deleteOlderThanEpoch(int $epochSec, int $batchSize = 1000): int
    {
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $total = 0;
        while (true) {
            if ($driver === 'pgsql') {
                $sel = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE ts < to_timestamp(:ts) ORDER BY id ASC LIMIT :lim");
            } else {
                // SQLite
                $sel = $this->pdo->prepare("SELECT id FROM {$this->table} WHERE ts < :ts ORDER BY id ASC LIMIT :lim");
            }
            $sel->bindValue(':ts', $epochSec, PDO::PARAM_INT);
            $sel->bindValue(':lim', $batchSize, PDO::PARAM_INT);
            $sel->execute();
            $ids = $sel->fetchAll(PDO::FETCH_COLUMN, 0);
            if (!$ids) break;

            $in = implode(',', array_fill(0, count($ids), '?'));
            $del = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id IN ($in)");
            $del->execute(array_map('intval', $ids));
            $affected = $del->rowCount();
            $total += $affected;
            if ($affected < $batchSize) break;
        }
        return $total;
    }
}
