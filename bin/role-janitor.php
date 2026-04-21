#!/usr/bin/env php
<?php
declare(strict_types=1);

use App\Rolling\Infrastructure\Housekeeping\{PdoAuditGc, PdoReplayGc, Janitor};
use App\Rolling\Infrastructure\Housekeeping\Archive\JsonlAuditArchiver;

require __DIR__ . '/../vendor/autoload.php';

/**
 * @param string $path
 * @return array
 */
function loadCfg(string $path): array
{
    if (!file_exists($path)) return [];
    $data = json_decode((string)file_get_contents($path), true);
    return is_array($data) ? $data : [];
}

$cmd = $argv[1] ?? 'help';
switch ($cmd) {
    case 'gc':
        $dsn = getenv('ROLE_AUDIT_DSN') ?: 'sqlite::memory:';
        $cfgPath = $argv[2] ?? __DIR__ . '/../ops/retention.json';
        $pdo = new PDO($dsn);
        if (str_starts_with($dsn, 'sqlite:')) {
            // Ensure tables exist for sqlite demo
            @ $pdo->exec("CREATE TABLE IF NOT EXISTS role_audit(id INTEGER PRIMARY KEY AUTOINCREMENT, ts INTEGER, subject_id TEXT, action TEXT, scope_key TEXT, decision TEXT, reason TEXT, obligations TEXT, ctx TEXT)");
            @ $pdo->exec("CREATE TABLE IF NOT EXISTS replay_nonce(nonce TEXT PRIMARY KEY, expires_ts INTEGER NOT NULL)");
        }
        $jan = new Janitor($pdo, loadCfg($cfgPath));
        $res = $jan->run();
        echo json_encode($res, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        break;

    case 'gc-audit':
        $dsn = getenv('ROLE_AUDIT_DSN') ?: 'sqlite::memory:';
        $days = (int)($argv[2] ?? 30);
        $batch = (int)($argv[3] ?? 1000);
        $pdo = new PDO($dsn);
        $cut = time() - $days * 86400;
        $gc = new PdoAuditGc($pdo);
        $deleted = $gc->deleteOlderThanEpoch($cut, $batch);
        echo "deleted={$deleted}\n";
        break;

    case 'gc-replay':
        $dsn = getenv('ROLE_AUDIT_DSN') ?: 'sqlite::memory:';
        $batch = (int)($argv[2] ?? 5000);
        $pdo = new PDO($dsn);
        $gc = new PdoReplayGc($pdo);
        $deleted = $gc->deleteExpired(time(), $batch);
        echo "deleted={$deleted}\n";
        break;

    case 'archive-audit':
        $dsn = getenv('ROLE_AUDIT_DSN') ?: 'sqlite::memory:';
        $days = (int)($argv[2] ?? 60);
        $path = $argv[3] ?? (sys_get_temp_dir() . '/role_audit_archive.jsonl');
        $batch = (int)($argv[4] ?? 1000);
        $gzip = (bool)($argv[5] ?? false);
        $pdo = new PDO($dsn);
        $cut = time() - $days * 86400;
        $arch = new JsonlAuditArchiver($pdo);
        $res = $arch->archiveOlderThanEpoch($cut, $path, $batch, $gzip);
        echo json_encode($res, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        break;

    default:
        echo "Usage:\n";
        echo "  php bin/role-janitor.php gc [configPath]\n";
        echo "  php bin/role-janitor.php gc-audit [retainDays] [batch]\n";
        echo "  php bin/role-janitor.php gc-replay [batch]\n";
        echo "  php bin/role-janitor.php archive-audit [olderThanDays] [outPath] [batch] [gzip]\n";
        echo "Environment: ROLE_AUDIT_DSN\n";
}
