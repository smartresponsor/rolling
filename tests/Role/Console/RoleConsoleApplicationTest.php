<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Console;

use App\Rolling\Infrastructure\Console\RoleConsoleApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

final class RoleConsoleApplicationTest extends TestCase
{
    public function testPropagationPreviewCommandReturnsSuccessAndJsonPayload(): void
    {
        $application = new RoleConsoleApplication();

        $consoleOutput = new BufferedOutput();
        $exitCode = $application->run(['role-console', 'app:role:propagation:preview', 'propagation-chain'], $consoleOutput);
        $output = $consoleOutput->fetch();

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('"mode": "preview"', $output);
        self::assertStringContainsString('"scenario": "propagation"', $output);
    }

    public function testEliminationRunCommandReturnsSuccessAndJsonPayload(): void
    {
        $application = new RoleConsoleApplication();

        $consoleOutput = new BufferedOutput();
        $exitCode = $application->run(['role-console', 'app:role:elimination:run', 'elimination-cascade'], $consoleOutput);
        $output = $consoleOutput->fetch();

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('"mode": "run"', $output);
        self::assertStringContainsString('"scenario": "elimination"', $output);
    }
}
