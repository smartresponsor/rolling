<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Console;

use App\Rolling\Infrastructure\Console\RoleConsoleApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class RolePerfBaselineComparisonCommandTest extends TestCase
{
    public function testPerfRegressionCommandCanCompareAgainstBaseline(): void
    {
        $application = (new RoleConsoleApplication())->build();
        $tester = new CommandTester($application->find('app:role:perf:regression-check'));

        $baselineFile = tempnam(sys_get_temp_dir(), 'role-perf-baseline-');
        self::assertNotFalse($baselineFile);
        file_put_contents($baselineFile, json_encode([
            'kind' => 'perf',
            'stats' => [
                'duration_ms' => 999999.0,
                'per_item_ms' => 999999.0,
                'throughput_per_sec' => 1.0,
                'peak_mb' => 999999.0,
            ],
        ], JSON_THROW_ON_ERROR));

        try {
            self::assertSame(0, $tester->execute([
                'n' => '8',
                'sleep_us' => '0',
                'chunk' => '4',
                '--max-duration-ms' => '9999999',
                '--max-per-item-ms' => '9999999',
                '--min-throughput-per-sec' => '0.001',
                '--max-peak-mb' => '9999999',
                '--baseline' => $baselineFile,
                '--max-duration-regression-pct' => '100',
                '--max-per-item-regression-pct' => '100',
                '--max-peak-regression-pct' => '100',
                '--max-throughput-drop-pct' => '100',
            ]));

            $payload = json_decode($tester->getDisplay(), true, flags: JSON_THROW_ON_ERROR);
            self::assertArrayHasKey('comparison', $payload);
            self::assertTrue($payload['comparison']['ok']);
        } finally {
            self::removeFile($baselineFile);
        }
    }

    public function testBenchRegressionCommandCanFailAgainstBaseline(): void
    {
        $application = (new RoleConsoleApplication())->build();
        $tester = new CommandTester($application->find('app:role:bench:regression-check'));

        $baselineFile = tempnam(sys_get_temp_dir(), 'role-bench-baseline-');
        self::assertNotFalse($baselineFile);
        file_put_contents($baselineFile, json_encode([
            'kind' => 'bench',
            'stats' => [
                'summary' => [
                    [
                        'name' => 'micro',
                        'p95_ms' => 0.0001,
                        'p99_ms' => 0.0001,
                    ],
                    [
                        'name' => 'batch',
                        'per_item_ms' => 0.0001,
                    ],
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        try {
            self::assertSame(1, $tester->execute([
                'iterations' => '100',
                'batch_n' => '20',
                'rpc_us' => '500',
                '--max-p95-ms' => '999999',
                '--max-p99-ms' => '999999',
                '--max-batch-per-item-ms' => '999999',
                '--baseline' => $baselineFile,
                '--max-p95-regression-pct' => '0',
                '--max-p99-regression-pct' => '0',
                '--max-batch-per-item-regression-pct' => '0',
            ]));

            $payload = json_decode($tester->getDisplay(), true, flags: JSON_THROW_ON_ERROR);
            self::assertArrayHasKey('comparison', $payload);
            self::assertFalse($payload['comparison']['ok']);
        } finally {
            self::removeFile($baselineFile);
        }
    }

    private static function removeFile(string $path): void
    {
        if (is_file($path)) {
            unlink($path);
        }
    }
}
