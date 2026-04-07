<?php
declare(strict_types=1);

namespace Tests\Role\Audit;

use App\Infrastructure\Audit\{AuditRecord};
use App\InfrastructureInterface\Audit\Export\ExporterInterface;
use App\Infrastructure\Audit\Export\{JsonlExporter, RetryingExporter};
use PHPUnit\Framework\TestCase;
use RuntimeException;

/**
 *
 */

/**
 *
 */
final class ExporterTest extends TestCase
{
    /**
     * @return void
     */
    public function testJsonlExporterWritesLines(): void
    {
        $path = sys_get_temp_dir() . '/audit.jsonl';
        self::removeFile($path);

        $exp = new JsonlExporter();
        $recs = [
            new AuditRecord(1, 'u1', 'a', 'global', 'ALLOW', '', [], []),
            new AuditRecord(2, 'u2', 'b', 'tenant:t1', 'DENY', 'no', [], []),
        ];
        $exp->export($recs, $path);

        $lines = file($path, FILE_IGNORE_NEW_LINES);
        $this->assertCount(2, $lines);
        $row = json_decode($lines[0], true);
        $this->assertSame('u1', $row['subjectId']);
        self::removeFile($path);
    }

    /**
     * @return void
     */
    public function testRetryingExporterRetries(): void
    {
        $calls = 0;
        $inner = new class implements ExporterInterface {
            public int $calls = 0;

            /**
             * @param iterable $records
             * @param string $path
             * @return void
             */
            public function export(iterable $records, string $path): void
            {
                $this->calls++;
                if ($this->calls < 2) throw new RuntimeException('fail once');
                (new JsonlExporter())->export($records, $path);
            }
        };
        $exp = new RetryingExporter($inner, retries: 2, baseMs: 10);
        $path = sys_get_temp_dir() . '/audit_retry.jsonl';
        self::removeFile($path);
        try {
            $exp->export([new AuditRecord(1, 'u', 'a', 'global', 'ALLOW')], $path);
        } catch (\Throwable $e) {
        }
        $this->assertFileExists($path);
        self::removeFile($path);
    }

    private static function removeFile(string $path): void
    {
        if (is_file($path)) {
            unlink($path);
        }
    }
}
