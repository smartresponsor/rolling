<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:test:scenario', description: 'Run a named fixture scenario.')]
final class ScenarioRunCommand extends AbstractRoleCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('fixture', InputArgument::REQUIRED, 'Fixture name.')
            ->addArgument('scenario', InputArgument::REQUIRED, 'Scenario name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $fixture = $this->fixture((string) $input->getArgument('fixture'));
            $result = $this->runScenario($fixture, $this->scenario((string) $input->getArgument('scenario')));
            $this->writeJson($output, $result);

            return ($result['ok'] ?? false) ? self::SUCCESS : self::FAILURE;
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
