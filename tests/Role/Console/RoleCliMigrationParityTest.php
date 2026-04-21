<?php

declare(strict_types=1);

namespace App\Rolling\Tests\Role\Console;

use App\Rolling\Infrastructure\Console\RoleConsoleApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class RoleCliMigrationParityTest extends TestCase
{
    public function testMigratedPolicyAndJanitorCommandsAreRegistered(): void
    {
        $application = (new RoleConsoleApplication())->build();

        self::assertNotNull($application->find('app:role:policy:import'));
        self::assertNotNull($application->find('app:role:policy:activate'));
        self::assertNotNull($application->find('app:role:policy:export'));
        self::assertNotNull($application->find('app:role:policy:migrate'));
        self::assertNotNull($application->find('app:role:admin:policy:import'));
        self::assertNotNull($application->find('app:role:admin:policy:activate'));
        self::assertNotNull($application->find('app:role:admin:policy:export'));
        self::assertNotNull($application->find('app:role:janitor:gc-audit'));
        self::assertNotNull($application->find('app:role:janitor:gc-replay'));
        self::assertNotNull($application->find('app:role:janitor:archive-audit'));
    }

    public function testPolicyImportActivateAndExportRoundTripReturnsStructuredJson(): void
    {
        $application = (new RoleConsoleApplication())->build();
        $policyPath = sys_get_temp_dir().'/rolling-example-policy.json';
        file_put_contents($policyPath, json_encode([
            'roles' => [
                'viewer' => [
                    'allow' => ['message.read'],
                ],
            ],
        ], JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));

        $import = new CommandTester($application->find('app:role:policy:import'));
        self::assertSame(0, $import->execute(['name' => 'default-policy', 'version' => 'v1', 'file' => $policyPath]));

        $activate = new CommandTester($application->find('app:role:policy:activate'));
        self::assertSame(0, $activate->execute(['name' => 'default-policy', 'version' => 'v1']));

        $export = new CommandTester($application->find('app:role:policy:export'));
        self::assertSame(0, $export->execute(['name' => 'default-policy', 'version' => 'v1']));
        $payload = json_decode($export->getDisplay(), true, flags: JSON_THROW_ON_ERROR);

        self::assertTrue($payload['ok']);
        self::assertSame('default-policy', $payload['name']);
        self::assertStringContainsString('viewer', (string) $payload['document']);

        @unlink($policyPath);
    }

    public function testJanitorSpecializedCommandsReturnStructuredJson(): void
    {
        $application = (new RoleConsoleApplication())->build();

        $gcAudit = new CommandTester($application->find('app:role:janitor:gc-audit'));
        self::assertSame(0, $gcAudit->execute(['days' => '30', 'batch' => '1000', '--dsn' => 'sqlite::memory:']));
        $auditPayload = json_decode($gcAudit->getDisplay(), true, flags: JSON_THROW_ON_ERROR);
        self::assertTrue($auditPayload['ok']);
        self::assertArrayHasKey('deleted', $auditPayload);

        $gcReplay = new CommandTester($application->find('app:role:janitor:gc-replay'));
        self::assertSame(0, $gcReplay->execute(['batch' => '1000', '--dsn' => 'sqlite::memory:']));
        $replayPayload = json_decode($gcReplay->getDisplay(), true, flags: JSON_THROW_ON_ERROR);
        self::assertTrue($replayPayload['ok']);
        self::assertArrayHasKey('deleted', $replayPayload);
    }
}
