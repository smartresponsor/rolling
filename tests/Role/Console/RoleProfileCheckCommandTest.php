<?php

declare(strict_types=1);

namespace App\Tests\Role\Console;

use App\Infrastructure\Console\RoleConsoleApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class RoleProfileCheckCommandTest extends TestCase
{
    public function testPerfProfileCheckCanResolveBaselineFromManifest(): void
    {
        $baselineFile = tempnam(sys_get_temp_dir(), 'role-perf-baseline-');
        $manifestFile = tempnam(sys_get_temp_dir(), 'role-baseline-manifest-');
        self::assertNotFalse($baselineFile);
        self::assertNotFalse($manifestFile);

        file_put_contents($baselineFile, json_encode([
            'kind' => 'perf',
            'generated_at' => '2026-03-19T00:00:00+00:00',
            'stats' => [
                'duration_ms' => 999999.0,
                'per_item_ms' => 999999.0,
                'throughput_per_sec' => 1.0,
                'peak_mb' => 999999.0,
            ],
        ], JSON_THROW_ON_ERROR));
        file_put_contents($manifestFile, json_encode([
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
            $tester = new CommandTester($application->find('app:role:perf:profile-check'));
            self::assertSame(0, $tester->execute([
                'n' => '8',
                'sleep_us' => '0',
                'chunk' => '4',
                '--profile' => 'smoke',
                '--manifest' => $manifestFile,
            ]));

            $payload = json_decode($tester->getDisplay(), true, flags: JSON_THROW_ON_ERROR);
            self::assertSame('smoke', $payload['profile']);
            self::assertArrayHasKey('comparison', $payload);
            self::assertSame($baselineFile, $payload['comparison']['baseline']);
        } finally {
            self::removeFile($baselineFile);
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
