<?php

declare(strict_types=1);

namespace Tests\Role\Housekeeping;

use App\Infrastructure\Housekeeping\PdoAuditGc;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 *
 */

/**
 *
 */
final class AuditGcTest extends TestCase
{
    /**
     * @return void
     */
    public function testDeletesOlderThan(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite is not available in the local PHP CLI.');
        }

        $pdo = new PDO('sqlite::memory:');
        $pdo->exec('CREATE TABLE role_audit(id INTEGER PRIMARY KEY AUTOINCREMENT, ts INTEGER, subject_id TEXT, action TEXT, scope_key TEXT, decision TEXT, reason TEXT, obligations TEXT, ctx TEXT)');
        $now = 1_700_000_000;
        // two old, one fresh
        $pdo->exec('INSERT INTO role_audit(ts,subject_id,action,scope_key,decision) VALUES
            (' . ($now - 100000) . ",'u','a','g','ALLOW'),
            (" . ($now - 99999) . ",'u','a','g','ALLOW'),
            (" . ($now + 1) . ",'u','a','g','ALLOW')");
        $gc = new PdoAuditGc($pdo);
        $deleted = $gc->deleteOlderThanEpoch($now - 10, 100);
        $this->assertSame(2, $deleted);
        $cnt = (int) $pdo->query('SELECT COUNT(*) FROM role_audit')->fetchColumn();
        $this->assertSame(1, $cnt);
    }
}
