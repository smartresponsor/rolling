<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Console;

use App\Rolling\Infrastructure\Console\RoleConsoleApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class RoleBenchmarkCommandTest extends TestCase
{
    public function testBenchmarkCommandsAreRegistered(): void
    {
        $application = (new RoleConsoleApplication())->build();

        self::assertNotNull($application->find('app:role:batch:perf'));
        self::assertNotNull($application->find('app:role:bench:run'));
        self::assertNotNull($application->find('app:role:perf:stats'));
        self::assertNotNull($application->find('app:role:bench:stats'));
    }

    public function testBatchPerfCommandReturnsStructuredJson(): void
    {
        $application = (new RoleConsoleApplication())->build();
        $tester = new CommandTester($application->find('app:role:batch:perf'));

        self::assertSame(0, $tester->execute(['n' => '16', 'sleep_us' => '0', 'chunk' => '8']));
        $payload = json_decode($tester->getDisplay(), true, flags: JSON_THROW_ON_ERROR);

        self::assertSame(16, $payload['n']);
        self::assertArrayHasKey('stats', $payload);
        self::assertArrayHasKey('throughput_per_sec', $payload['stats']);
    }

    public function testBenchStatsCommandReturnsScenarioSummary(): void
    {
        $application = (new RoleConsoleApplication())->build();
        $tester = new CommandTester($application->find('app:role:bench:stats'));

        self::assertSame(0, $tester->execute(['iterations' => '32', 'batch_n' => '24', 'rpc_us' => '0']));
        $payload = json_decode($tester->getDisplay(), true, flags: JSON_THROW_ON_ERROR);

        self::assertTrue($payload['ok']);
        self::assertGreaterThan(0, $payload['stats']['scenario_count']);
        self::assertIsArray($payload['stats']['summary']);
    }
}
