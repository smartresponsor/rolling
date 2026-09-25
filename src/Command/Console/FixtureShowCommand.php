<?php

declare(strict_types=1);

namespace App\Rolling\Command\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:fixture:show', description: 'Print fixture payload as JSON.')]
/**
 * Implements the FixtureShowCommand console workflow.
 */
final class FixtureShowCommand extends AbstractRoleCommand
{
    /**
     * Configure command arguments and options.
     */
    protected function configure(): void
    {
        $this->addArgument('fixture', InputArgument::REQUIRED, 'Fixture nameEntity.');
    }

    /**
     * Execute the command and return a Symfony Console status code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            return $this->writeJson($output, $this->fixture((string) $input->getArgument('fixture')));
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
