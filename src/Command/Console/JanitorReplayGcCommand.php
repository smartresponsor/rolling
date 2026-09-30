<?php

declare(strict_types=1);

namespace App\Rolling\Command\Console;

use App\Rolling\Infrastructure\Console\Support\RoleConsoleRuntime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:janitor:gc-replay', description: 'Delete expired rows from replay_nonce.')]
/**
 * Implements the JanitorReplayGcCommand console workflow.
 */
final class JanitorReplayGcCommand extends AbstractRoleCommand
{
    public function __construct(private readonly RoleConsoleRuntime $runtime)
    {
        parent::__construct();
    }

    /**
     * Configure command arguments and options.
     */
    protected function configure(): void
    {
        $this
            ->addArgument('batch', InputArgument::OPTIONAL, 'Batch size.', '5000')
            ->addOption('dsn', null, InputOption::VALUE_REQUIRED, 'Audit DSN override.');
    }

    /**
     * Execute the command and return a Symfony Console status code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $dsn = (string) ($input->getOption('dsn') ?: $this->runtime->auditDsn());

            return $this->writeJson($output, $this->runtime->janitorReplayGc($dsn, (int) $input->getArgument('batch')));
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
