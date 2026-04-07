<?php

declare(strict_types=1);

namespace App\Tests\Role\Console;

use App\Infrastructure\Console\RoleConsoleApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class RoleOperationalCommandTest extends TestCase
{
    public function testOperationalCommandsAreRegisteredInSymfonyConsoleLayer(): void
    {
        $application = (new RoleConsoleApplication())->build();

        self::assertNotNull($application->find('app:role:rebac:check'));
        self::assertNotNull($application->find('app:role:policy:list'));
        self::assertNotNull($application->find('app:role:admin:rebac:stats'));
        self::assertNotNull($application->find('app:role:janitor:gc'));
    }

    public function testRebacCheckCommandReturnsJsonPayload(): void
    {
        $application = (new RoleConsoleApplication())->build();
        $tester = new CommandTester($application->find('app:role:rebac:check'));

        $exitCode = $tester->execute([
            'subject' => 'user:42',
            'object' => 'doc:1',
            'relation' => 'viewer',
        ]);

        self::assertSame(1, $exitCode);
        $payload = json_decode($tester->getDisplay(), true, flags: JSON_THROW_ON_ERROR);
        self::assertFalse($payload['allow']);
        self::assertSame('default', $payload['ns']);
    }

    public function testPolicyListCommandReturnsStructuredJsonEvenForEmptyStore(): void
    {
        $application = (new RoleConsoleApplication())->build();
        $tester = new CommandTester($application->find('app:role:policy:list'));

        $exitCode = $tester->execute(['name' => 'default-policy']);

        self::assertSame(0, $exitCode);
        $payload = json_decode($tester->getDisplay(), true, flags: JSON_THROW_ON_ERROR);
        self::assertTrue($payload['ok']);
        self::assertSame('default-policy', $payload['name']);
        self::assertSame([], $payload['versions']);
    }

    public function testJanitorGcCommandReturnsJsonPayload(): void
    {
        $application = (new RoleConsoleApplication())->build();
        $tester = new CommandTester($application->find('app:role:janitor:gc'));

        $exitCode = $tester->execute([
            '--dsn' => 'sqlite::memory:',
            '--config' => __DIR__ . '/../../../misc/ops/retention.json',
        ]);

        self::assertSame(0, $exitCode);
        $payload = json_decode($tester->getDisplay(), true, flags: JSON_THROW_ON_ERROR);
        self::assertArrayHasKey('audit_gc_deleted', $payload);
        self::assertArrayHasKey('replay_gc_deleted', $payload);
    }
}
