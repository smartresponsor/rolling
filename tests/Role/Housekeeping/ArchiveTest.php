<?php

declare(strict_types=1);

namespace Tests\Role\Housekeeping;

use App\Housekeeping\Role\Archive\JsonlAuditArchiver;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 *
 */

/**
 *
 */
final class ArchiveTest extends TestCase
{
    /**
     * @return void
     */
    public function testArchiveAndDelete(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite is not available in the local PHP CLI.');
        }

        $pdo = new PDO('sqlite::memory:');
        $pdo->exec('CREATE TABLE role_audit(id INTEGER PRIMARY KEY AUTOINCREMENT, ts INTEGER, subject_id TEXT, action TEXT, scope_key TEXT, decision TEXT, reason TEXT, obligations TEXT, ctx TEXT)');
        for ($i = 0; $i < 5; $i++) {
            $pdo->exec("INSERT INTO role_audit(ts,subject_id,action,scope_key,decision,obligations,ctx) VALUES (1,'u','a','g','ALLOW','{}','{}')");
        }
        $arch = new JsonlAuditArchiver($pdo);
        $path = sys_get_temp_dir() . '/audit_arch_test.jsonl';
        @unlink($path);
        $res = $arch->archiveOlderThanEpoch(10, $path, 2);
        $this->assertSame(5, $res['exported']);
        $this->assertSame(5, $res['deleted']);
        $this->assertFileExists($path);
        $lines = file($path, FILE_IGNORE_NEW_LINES);
        $this->assertCount(5, $lines);
        @unlink($path);
    }
}
