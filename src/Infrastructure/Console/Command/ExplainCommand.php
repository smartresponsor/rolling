<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:explain', description: 'Explain a role access check for a fixture.')]
final class ExplainCommand extends AbstractRoleCommand
{
    protected function configure(): void
    {
        $this
            ->addArgument('fixture', InputArgument::REQUIRED, 'Fixture name.')
            ->addArgument('subject', InputArgument::REQUIRED, 'Subject identifier.')
            ->addArgument('object', InputArgument::REQUIRED, 'Object identifier.')
            ->addArgument('relation', InputArgument::REQUIRED, 'Relation name.')
            ->addArgument('scenario', InputArgument::OPTIONAL, 'Optional scenario name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $fixture = $this->fixture((string) $input->getArgument('fixture'));

            return $this->writeJson($output, $this->explain(
                $fixture,
                (string) $input->getArgument('subject'),
                (string) $input->getArgument('object'),
                (string) $input->getArgument('relation'),
                (null !== $input->getArgument('scenario')) ? (string) $input->getArgument('scenario') : null,
            ));
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
