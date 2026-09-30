<?php

declare(strict_types=1);

namespace App\Rolling\Command\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:fixture:list', description: 'List available role fixtures.')]
/**
 * Implements the FixtureListCommand console workflow.
 */
final class FixtureListCommand extends AbstractRoleCommand
{
    /**
     * Execute the command and return a Symfony Console status code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        foreach ($this->fixtureNames() as $nameEntity) {
            $output->writeln($nameEntity);
        }

        return self::SUCCESS;
    }
}
