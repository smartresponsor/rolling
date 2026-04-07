<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Infrastructure\Console\Contract\RoleCommandRegistryInterface;
use App\Infrastructure\Console\Support\DefaultRoleCommandFactory;
use App\Infrastructure\Console\Support\DefaultRoleCommandRegistry;
use App\Infrastructure\Console\Support\RoleConsoleRuntime;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

final class RoleConsoleApplication
{
    public function __construct(private readonly ?RoleCommandRegistryInterface $registry = null)
    {
    }

    public function build(): Application
    {
        $application = new Application('SmartResponsor Role Console', 'w28');
        $application->setDefaultCommand('app:role:fixture:list');

        foreach ($this->registry()->all() as $command) {
            $application->addCommand($command);
        }

        return $application;
    }

    public function run(array $argv): int
    {
        return $this->build()->run(new ArgvInput($argv), new ConsoleOutput());
    }

    private function registry(): RoleCommandRegistryInterface
    {
        if ($this->registry instanceof RoleCommandRegistryInterface) {
            return $this->registry;
        }

        return new DefaultRoleCommandRegistry(new DefaultRoleCommandFactory(new RoleConsoleRuntime()));
    }
}
