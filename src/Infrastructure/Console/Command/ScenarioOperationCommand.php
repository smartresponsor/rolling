<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class ScenarioOperationCommand extends AbstractRoleCommand
{
    public function __construct(
        private readonly string $commandName,
        private readonly string $scenarioName,
        private readonly string $mode,
        private readonly string $description,
    ) {
        parent::__construct($commandName);
        $this->setDescription($description);
    }

    protected function configure(): void
    {
        $this->addArgument('fixture', InputArgument::REQUIRED, 'Fixture name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $fixture = $this->fixture((string) $input->getArgument('fixture'));
            $result = 'preview' === $this->mode
                ? $this->preview($fixture, $this->scenarioName)
                : $this->runScenario($fixture, $this->scenarioName);

            return $this->writeJson($output, $result);
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
