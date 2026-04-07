<?php
declare(strict_types=1);

namespace App\Infrastructure\Housekeeping\Archive;

use PDO;
use RuntimeException;

/**
 * Архивирует строки из role_audit старше порога в JSONL файл и удаляет их из БД.
 * Формат строки: исходный набор колонок SELECT *.
 */
final class JsonlAuditArchiver
{
    /**
     * @param \PDO $pdo
     * @param string $table
     */
    public function __construct(private readonly PDO $pdo, private readonly string $table = 'role_audit')
    {
    }

    /**
     * @return array{exported:int, deleted:int, path:string}
     */
    public function archiveOlderThanEpoch(int $epochSec, string $path, int $batchSize = 1000, bool $gzip = false): array
    {
        $driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $resolvedPath = str_contains($path, '%') ? strftime($path) : $path;
        $directory = dirname($resolvedPath);
        if (!is_dir($directory)) {
            @mkdir($directory, 0777, true);
        }
        $fh = @fopen($resolvedPath, 'ab');
        if ($fh === false) {
            $resolvedPath = sys_get_temp_dir() . '/' . basename($resolvedPath);
            $fh = @fopen($resolvedPath, 'ab');
        }
        if ($fh === false) {
            throw new RuntimeException("Cannot open $resolvedPath");
        }

        $exported = 0;
        $deleted = 0;
        while (true) {
            if ($driver === 'pgsql') {
                $stmt = $this->pdo->prepare("SELECT id, ts, subject_id, action, scope_key, decision, reason, obligations, ctx FROM {$this->table} WHERE ts < to_timestamp(:ts) ORDER BY id ASC LIMIT :lim");
            } else {
                $stmt = $this->pdo->prepare("SELECT id, ts, subject_id, action, scope_key, decision, reason, obligations, ctx FROM {$this->table} WHERE ts < :ts ORDER BY id ASC LIMIT :lim");
            }
            $stmt->bindValue(':ts', $epochSec, PDO::PARAM_INT);
            $stmt->bindValue(':lim', $batchSize, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (!$rows) break;

            foreach ($rows as $r) {
                fwrite($fh, json_encode($r, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
            }
            $exported += count($rows);

            $in = implode(',', array_fill(0, count($rows), '?'));
            $del = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id IN ($in)");
            $del->execute(array_map(fn($r) => (int)$r['id'], $rows));
            $deleted += $del->rowCount();

            if (count($rows) < $batchSize) break;
        }
        fclose($fh);

        if ($gzip && $exported > 0) {
            $gz = $resolvedPath . '.gz';
            $data = file_get_contents($resolvedPath);
            if ($data !== false) {
                file_put_contents($gz, gzencode($data, 6));
                if (is_file($resolvedPath)) {
                    unlink($resolvedPath);
                }
                $resolvedPath = $gz;
            }
        }

        return ['exported' => $exported, 'deleted' => $deleted, 'path' => $resolvedPath];
    }
}
