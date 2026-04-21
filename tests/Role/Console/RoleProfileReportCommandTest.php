<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Console;

use App\Rolling\Infrastructure\Console\RoleConsoleApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class RoleProfileReportCommandTest extends TestCase
{
    public function testProfileReportCanRenderManifestSummary(): void
    {
        $baselineFile = tempnam(sys_get_temp_dir(), 'role-profile-summary-baseline-');
        $manifestFile = tempnam(sys_get_temp_dir(), 'role-profile-summary-manifest-');
        self::assertNotFalse($baselineFile);
        self::assertNotFalse($manifestFile);

        file_put_contents((string) $baselineFile, json_encode([
            'kind' => 'perf',
            'generated_at' => '2026-03-19T00:00:00+00:00',
            'stats' => [
                'duration_ms' => 12.0,
                'per_item_ms' => 0.12,
                'throughput_per_sec' => 900.0,
                'peak_mb' => 32.0,
            ],
        ], JSON_THROW_ON_ERROR));
        file_put_contents((string) $manifestFile, json_encode([
            'generated_at' => '2026-03-19T00:00:00+00:00',
            'baselines' => [
                'perf:smoke' => [
                    'kind' => 'perf',
                    'profile' => 'smoke',
                    'report_path' => $baselineFile,
                ],
            ],
        ], JSON_THROW_ON_ERROR));

        try {
            $application = (new RoleConsoleApplication())->build();
            $tester = new CommandTester($application->find('app:role:profile:report'));
            self::assertSame(0, $tester->execute([
                'kind' => 'perf',
                '--manifest' => $manifestFile,
            ]));

            $payload = json_decode($tester->getDisplay(), true, flags: JSON_THROW_ON_ERROR);
            self::assertArrayHasKey('kinds', $payload);
            self::assertSame($baselineFile, $payload['kinds']['perf']['profiles']['smoke']['baseline']);
            self::assertSame(12.0, $payload['kinds']['perf']['profiles']['smoke']['stats']['duration_ms']);
        } finally {
            self::removeFile((string) $baselineFile);
            self::removeFile((string) $manifestFile);
        }
    }

    private static function removeFile(string $path): void
    {
        if (is_file($path)) {
            unlink($path);
        }
    }
}
