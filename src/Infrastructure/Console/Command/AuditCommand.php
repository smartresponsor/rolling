<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:audit', description: 'Run baseline and scenario audit for a fixture.')]
final class AuditCommand extends AbstractRoleCommand
{
    protected function configure(): void
    {
        $this->addArgument('fixture', InputArgument::REQUIRED, 'Fixture name.');
    }

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
