<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Console;

use App\Rolling\Infrastructure\Console\RoleConsoleApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class RoleBenchmarkReportCommandTest extends TestCase
{
    public function testReportCommandsAreRegistered(): void
    {
        $application = (new RoleConsoleApplication())->build();

        self::assertNotNull($application->find('app:role:perf:report'));
        self::assertNotNull($application->find('app:role:bench:report'));
    }

    public function testPerfReportCommandCanPersistOutput(): void
    {
        $application = (new RoleConsoleApplication())->build();
        $tester = new CommandTester($application->find('app:role:perf:report'));
        $output = sys_get_temp_dir().'/rolling_w24_perf_report.json';
        self::removeFile($output);

        self::assertSame(0, $tester->execute([
            'n' => '8',
            'sleep_us' => '0',
            'chunk' => '4',
            '--output' => $output,
            '--trace' => true,
            '--detailed' => true,
        ]));

        $payload = json_decode($tester->getDisplay(), true, flags: JSON_THROW_ON_ERROR);
        self::assertSame($output, $payload['output']);
        self::assertFileExists($output);
    }

    private static function removeFile(string $path): void
    {
        if (is_file($path)) {
            unlink($path);
        }
    }
}
