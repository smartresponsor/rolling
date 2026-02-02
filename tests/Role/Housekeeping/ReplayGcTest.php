<?php
declare(strict_types=1);

namespace Tests\Role\Housekeeping;

use App\Housekeeping\Role\PdoReplayGc;
use PDO;
use PHPUnit\Framework\TestCase;

/**
 *
 */

/**
 *
 */
final class ReplayGcTest extends TestCase
{
    /**
     * @return void
     */
    public function testDeletesExpired(): void
    {
        $pdo = new PDO('sqlite::memory:');
        $pdo->exec("CREATE TABLE replay_nonce(nonce TEXT PRIMARY KEY, expires_ts INTEGER NOT NULL)");
        $pdo->exec("INSERT INTO replay_nonce VALUES ('a', 100),('b',200),('c',300)");
        $gc = new PdoReplayGc($pdo);
        $deleted = $gc->deleteExpired(250, 10);
        $this->assertSame(2, $deleted);
        $cnt = (int)$pdo->query('SELECT COUNT(*) FROM replay_nonce')->fetchColumn();
        $this->assertSame(1, $cnt);
    }
}
