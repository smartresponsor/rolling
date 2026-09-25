<?php

declare(strict_types=1);

namespace App\Rolling\Command\Console;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:explain', description: 'Explain a role access check for a fixture.')]
/**
 * Implements the ExplainCommand console workflow.
 */
final class ExplainCommand extends AbstractRoleCommand
{
    /**
     * Configure command arguments and options.
     */
    protected function configure(): void
    {
        $this
            ->addArgument('fixture', InputArgument::REQUIRED, 'Fixture nameEntity.')
            ->addArgument('subject', InputArgument::REQUIRED, 'Subject identifier.')
            ->addArgument('object', InputArgument::REQUIRED, 'Object identifier.')
            ->addArgument('relation', InputArgument::REQUIRED, 'Relation nameEntity.')
            ->addArgument('scenario', InputArgument::OPTIONAL, 'Optional scenario nameEntity.');
    }

    /**
     * Execute the command and return a Symfony Console status code.
     */
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
