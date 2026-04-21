<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Console;

use App\Rolling\Infrastructure\Console\RoleConsoleApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class RoleBaselineManifestCommandTest extends TestCase
{
    public function testPerfBaselineCanBePromotedIntoManifest(): void
    {
        $reportFile = tempnam(sys_get_temp_dir(), 'role-perf-report-');
        $manifestFile = tempnam(sys_get_temp_dir(), 'role-baseline-manifest-');
        self::assertNotFalse($reportFile);
        self::assertNotFalse($manifestFile);
        file_put_contents($reportFile, json_encode([
            'kind' => 'perf',
            'generated_at' => '2026-03-19T00:00:00+00:00',
            'stats' => ['duration_ms' => 1.0],
        ], JSON_THROW_ON_ERROR));
        self::removeFile($manifestFile);

        try {
            $application = (new RoleConsoleApplication())->build();
            $tester = new CommandTester($application->find('app:role:perf:baseline:promote'));
            self::assertSame(0, $tester->execute([
                'report' => $reportFile,
                '--profile' => 'smoke',
                '--manifest' => $manifestFile,
                '--label' => 'ci-known-good',
            ]));
            $payload = json_decode($tester->getDisplay(), true, flags: JSON_THROW_ON_ERROR);
            self::assertTrue($payload['ok']);
            self::assertSame('perf', $payload['baseline']['kind']);
            self::assertFileExists($manifestFile);
            $manifest = json_decode((string) file_get_contents($manifestFile), true, flags: JSON_THROW_ON_ERROR);
            self::assertArrayHasKey('perf:smoke', $manifest['baselines']);
        } finally {
            self::removeFile($reportFile);
            self::removeFile($manifestFile);
        }
    }

    private static function removeFile(string $path): void
    {
        if (is_file($path)) {
            unlink($path);
        }
    }
}
