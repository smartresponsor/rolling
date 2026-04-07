<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:fixture:list', description: 'List available role fixtures.')]
final class FixtureListCommand extends AbstractRoleCommand
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->fixtureNames() as $name) {
            $output->writeln($name);
        }

        return self::SUCCESS;
    }
}
