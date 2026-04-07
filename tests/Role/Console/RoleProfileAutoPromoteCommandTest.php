<?php

declare(strict_types=1);

namespace App\Tests\Role\Console;

use App\Infrastructure\Console\RoleConsoleApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class RoleProfileAutoPromoteCommandTest extends TestCase
{
    public function testProfilePromoteCanInferProfileFromReport(): void
    {
        $reportFile = tempnam(sys_get_temp_dir(), 'role-profile-promote-report-');
        $manifestFile = tempnam(sys_get_temp_dir(), 'role-profile-promote-manifest-');
        self::assertNotFalse($reportFile);
        self::assertNotFalse($manifestFile);

        file_put_contents((string) $reportFile, json_encode([
            'kind' => 'perf',
            'profile' => 'smoke',
            'generated_at' => '2026-03-19T00:00:00+00:00',
            'gating' => ['ok' => true],
            'comparison' => ['ok' => true],
            'stats' => ['duration_ms' => 1.0],
        ], JSON_THROW_ON_ERROR));

        try {
            $application = (new RoleConsoleApplication())->build();
            $tester = new CommandTester($application->find('app:role:profile:promote'));
            self::assertSame(0, $tester->execute([
                'kind' => 'perf',
                'report' => $reportFile,
                '--manifest' => $manifestFile,
                '--require-passing' => true,
            ]));

            $payload = json_decode($tester->getDisplay(), true, flags: JSON_THROW_ON_ERROR);
            self::assertSame('smoke', $payload['profile']);
            self::assertSame('perf', $payload['baseline']['kind']);
        } finally {
            self::removeFile((string) $reportFile);
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
