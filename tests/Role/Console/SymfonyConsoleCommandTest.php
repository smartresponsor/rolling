<?php

declare(strict_types=1);

namespace App\Tests\Role\Console;

use App\Infrastructure\Console\RoleConsoleApplication;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Tester\CommandTester;

final class SymfonyConsoleCommandTest extends TestCase
{
    public function testFixtureListCommandIsRegisteredInSymfonyApplication(): void
    {
        $application = (new RoleConsoleApplication())->build();
        $command = $application->find('app:role:fixture:list');
        $tester = new CommandTester($command);

        $exitCode = $tester->execute([]);

        self::assertSame(0, $exitCode);
        self::assertStringContainsString('tenant-basic', $tester->getDisplay());
    }

    public function testExplainCommandReturnsJsonPayloadThroughCommandTester(): void
    {
        $application = (new RoleConsoleApplication())->build();
        $command = $application->find('app:role:explain');
        $tester = new CommandTester($command);

        $exitCode = $tester->execute([
            'fixture' => 'tenant-basic',
            'subject' => 'user:42',
            'object' => 'doc:1',
            'relation' => 'viewer',
        ]);

        self::assertSame(0, $exitCode);
        $payload = json_decode($tester->getDisplay(), true, flags: JSON_THROW_ON_ERROR);
        self::assertTrue($payload['summary']['allow']);
        self::assertSame('user:42', $payload['explanation']['subject']);
    }
}
