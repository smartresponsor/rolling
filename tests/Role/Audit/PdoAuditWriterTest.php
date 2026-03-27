<?php

declare(strict_types=1);

namespace Tests\Role\Audit;

use App\Audit\Role\{AuditRecord, PdoAuditWriter};
use PDO;
use PHPUnit\Framework\TestCase;

/**
 *
 */

/**
 *
 */
final class PdoAuditWriterTest extends TestCase
{
    /**
     * @return void
     */
    public function testInsertRecord(): void
    {
        if (!extension_loaded('pdo_sqlite')) {
            $this->markTestSkipped('pdo_sqlite is not available in the local PHP CLI.');
        }

        $pdo = new PDO('sqlite::memory:');
        $pdo->exec(file_get_contents(__DIR__ . '/../../../ops/db/sqlite/role_audit.sql'));

        $w = new PdoAuditWriter($pdo);
        $rec = new AuditRecord(time(), 'u1', 'message.read', 'tenant:t1', 'ALLOW', 'ok', ['types' => ['redact_fields'], 'count' => 1], ['ip' => '127.0.0.1', 'email' => 'a@b.c']);
        $w->write($rec);

        $cnt = (int) $pdo->query('SELECT COUNT(*) FROM role_audit')->fetchColumn();
        $this->assertSame(1, $cnt);
    }
}
