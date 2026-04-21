<?php

declare(strict_types=1);

namespace App\Rolling\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:fixtures:dry-run', description: 'Validates local sample fixtures without writing application state')]
final class FixturesDryRunCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fixtures = [
            ['subjectId' => 'u1', 'action' => 'message.read', 'scopeType' => 'tenant', 'tenantId' => 't1'],
            ['subjectId' => 'u2', 'action' => 'message.write', 'scopeType' => 'resource', 'tenantId' => 't1', 'resourceId' => 'doc-42'],
        ];

        foreach ($fixtures as $index => $fixture) {
            foreach (['subjectId', 'action', 'scopeType'] as $required) {
                if (!isset($fixture[$required]) || '' === $fixture[$required]) {
                    $output->writeln(sprintf('Fixture %d is invalid: missing %s', $index, $required));

                    return Command::FAILURE;
                }
            }
        }

        $output->writeln(sprintf('Validated %d fixtures in dry-run mode.', count($fixtures)));

        return Command::SUCCESS;
    }
}
