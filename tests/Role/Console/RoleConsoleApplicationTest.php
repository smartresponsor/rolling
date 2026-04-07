<?php

declare(strict_types=1);

namespace App\Tests\Role\Console;

use App\Infrastructure\Console\RoleConsoleApplication;
use PHPUnit\Framework\TestCase;

final class RoleConsoleApplicationTest extends TestCase
{
    public function testPropagationPreviewCommandReturnsSuccessAndJsonPayload(): void
    {
        $application = new RoleConsoleApplication();

        ob_start();
        $exitCode = $application->run(['bin/console', 'app:role:propagation:preview', 'propagation-chain']);
        $output = (string) ob_get_clean();

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('"mode": "preview"', $output);
        self::assertStringContainsString('"scenario": "propagation"', $output);
    }

    public function testEliminationRunCommandReturnsSuccessAndJsonPayload(): void
    {
        $application = new RoleConsoleApplication();

        ob_start();
        $exitCode = $application->run(['bin/console', 'app:role:elimination:run', 'elimination-cascade']);
        $output = (string) ob_get_clean();

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('"mode": "run"', $output);
        self::assertStringContainsString('"scenario": "elimination"', $output);
    }
}
