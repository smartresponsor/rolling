<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Console;

use App\Rolling\Infrastructure\Console\RoleConsoleApplication;
use PHPUnit\Framework\TestCase;

final class RoleConsoleDiagnosticsTest extends TestCase
{
    public function testExplainCommandPrintsExplanationPayload(): void
    {
        $app = new RoleConsoleApplication();

        ob_start();
        $exitCode = $app->run(['role-console', 'app:role:explain', 'tenant-basic', 'user:42', 'doc:1', 'viewer']);
        $output = (string) ob_get_clean();

        self::assertSame(0, $exitCode);
        $payload = json_decode($output, true, flags: JSON_THROW_ON_ERROR);
        self::assertTrue($payload['summary']['allow']);
        self::assertSame('user:42', $payload['explanation']['subject']);
    }

    public function testAuditCommandPrintsScenarioSummary(): void
    {
        $app = new RoleConsoleApplication();

        ob_start();
        $exitCode = $app->run(['role-console', 'app:role:audit', 'multi-tenant-isolation']);
        $output = (string) ob_get_clean();

        self::assertSame(0, $exitCode);
        $payload = json_decode($output, true, flags: JSON_THROW_ON_ERROR);
        self::assertTrue($payload['ok']);
        self::assertSame(1, $payload['summary']['scenario_count']);
        self::assertArrayHasKey('propagation', $payload['scenarios']);
    }
}
