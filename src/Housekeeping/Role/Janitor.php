<?php
declare(strict_types=1);

namespace App\Housekeeping\Role;

use App\Housekeeping\Role\Archive\JsonlAuditArchiver;
use PDO;

/** Оркестратор: читает config и запускает архив/GC. */
final class Janitor
{
    /**
     * @param \PDO $pdo
     * @param array $cfg
     */
    public function __construct(private readonly PDO $pdo, private readonly array $cfg)
    {
    }

    /**
     * @return array
     */
    public function run(): array
    {
        $now = Clock::now();
        $out = [];

        // Audit
        $audit = (array)($this->cfg['audit'] ?? []);
        $retDays = (int)($audit['retain_days'] ?? 30);
        $archive = (bool)($audit['archive_before_delete'] ?? true);
        $batch = (int)($audit['batch'] ?? 1000);
        $cut = $now - $retDays * 86400;

        if ($archive) {
            $path = (string)($audit['archive_path'] ?? sys_get_temp_dir() . '/role_audit_archive.jsonl');
            $gzip = (bool)($audit['gzip'] ?? true);
            $arch = new JsonlAuditArchiver($this->pdo);
            $res = $arch->archiveOlderThanEpoch($cut, $path, $batch, $gzip);
            $out['audit_archive'] = $res;
        }

        $gc = new PdoAuditGc($this->pdo);
        $deleted = $gc->deleteOlderThanEpoch($cut, $batch);
        $out['audit_gc_deleted'] = $deleted;

        // Replay
        $replay = (array)($this->cfg['replay'] ?? []);
        $replayBatch = (int)($replay['batch'] ?? 5000);
        $gcR = new PdoReplayGc($this->pdo);
        $out['replay_gc_deleted'] = $gcR->deleteExpired($now, $replayBatch);

        return $out;
    }
}
