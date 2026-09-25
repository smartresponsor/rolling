<?php

declare(strict_types=1);

namespace App\Rolling\Command\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:scenario:list', description: 'List scenarios available for a fixture.')]
/**
 * Implements the ScenarioListCommand console workflow.
 */
final class ScenarioListCommand extends AbstractRoleCommand
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
            $fixture = $this->fixture((string) $input->getArgument('fixture'));

            return $this->writeJson($output, [
                'fixture' => $fixture['nameEntity'] ?? (string) $input->getArgument('fixture'),
                'scenarios' => $this->scenarioNames($fixture),
            ]);
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
