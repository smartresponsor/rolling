<?php

declare(strict_types=1);

namespace App\Tests\Role\Console;

use App\Infrastructure\Console\RoleConsoleApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class RolePerfRegressionCommandTest extends TestCase
{
    public function testRegressionCommandsAreRegistered(): void
    {
        $application = (new RoleConsoleApplication())->build();

        self::assertNotNull($application->find('app:role:perf:regression-check'));
        self::assertNotNull($application->find('app:role:bench:regression-check'));
    }

    public function testPerfRegressionCommandCanPassThresholds(): void
    {
        $application = (new RoleConsoleApplication())->build();
        $tester = new CommandTester($application->find('app:role:perf:regression-check'));

        self::assertSame(0, $tester->execute([
            'n' => '8',
            'sleep_us' => '0',
            'chunk' => '4',
            '--max-duration-ms' => '5000',
            '--max-per-item-ms' => '50',
            '--min-throughput-per-sec' => '1',
            '--max-peak-mb' => '512',
        ]));

        $payload = json_decode($tester->getDisplay(), true, flags: JSON_THROW_ON_ERROR);
        self::assertTrue($payload['gating']['ok']);
    }

    public function testBenchRegressionCommandCanFailThresholds(): void
    {
        $application = (new RoleConsoleApplication())->build();
        $tester = new CommandTester($application->find('app:role:bench:regression-check'));

        self::assertSame(1, $tester->execute([
            'iterations' => '100',
            'batch_n' => '20',
            'rpc_us' => '500',
            '--max-p95-ms' => '0.001',
            '--max-p99-ms' => '0.001',
            '--max-batch-per-item-ms' => '0.001',
        ]));

        $payload = json_decode($tester->getDisplay(), true, flags: JSON_THROW_ON_ERROR);
        self::assertFalse($payload['gating']['ok']);
        self::assertGreaterThan(0, $payload['gating']['failure_count']);
    }
}
