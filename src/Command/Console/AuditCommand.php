<?php

declare(strict_types=1);

namespace App\Rolling\Command\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:audit', description: 'Run baseline and scenario audit for a fixture.')]
/**
 * Implements the AuditCommand console workflow.
 */
final class AuditCommand extends AbstractRoleCommand
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
            $result = $this->audit($this->fixture((string) $input->getArgument('fixture')));
            $this->writeJson($output, $result);

            return ($result['ok'] ?? false) ? self::SUCCESS : self::FAILURE;
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
