<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Housekeeping;

/** Чистит просроченные nonce из таблицы replay_nonce. */
final class PdoReplayGc
{
    /**
     * @param \PDO   $pdo
     * @param string $table
     */
    public function __construct(private readonly \PDO $pdo, private readonly string $table = 'replay_nonce')
    {
    }

    /**
     * @param int $nowEpoch
     * @param int $batchSize
     *
     * @return int
     */
    public function deleteExpired(int $nowEpoch, int $batchSize = 1000): int
    {
        $total = 0;
        while (true) {
            $sel = $this->pdo->prepare("SELECT nonce FROM {$this->table} WHERE expires_ts < :now LIMIT :lim");
            $sel->bindValue(':now', $nowEpoch, \PDO::PARAM_INT);
            $sel->bindValue(':lim', $batchSize, \PDO::PARAM_INT);
            $sel->execute();
            $ids = $sel->fetchAll(\PDO::FETCH_COLUMN, 0);
            if (!$ids) {
                break;
            }
            $in = implode(',', array_fill(0, count($ids), '?'));
            $del = $this->pdo->prepare("DELETE FROM {$this->table} WHERE nonce IN ($in)");
            $del->execute(array_map('strval', $ids));
            $aff = $del->rowCount();
            $total += $aff;
            if ($aff < $batchSize) {
                break;
            }
        }

        return $total;
    }
}
