<?php

declare(strict_types=1);

namespace App\Rolling\Command\Console;

use App\Rolling\Infrastructure\Console\Support\RoleConsoleRuntime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:janitor:gc-audit', description: 'Delete old rows from role_audit.')]
/**
 * Implements the JanitorAuditGcCommand console workflow.
 */
final class JanitorAuditGcCommand extends AbstractRoleCommand
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
            ->addArgument('days', InputArgument::OPTIONAL, 'Retention days.', '30')
            ->addArgument('batch', InputArgument::OPTIONAL, 'Batch size.', '1000')
            ->addOption('dsn', null, InputOption::VALUE_REQUIRED, 'Audit DSN override.');
    }

    /**
     * Execute the command and return a Symfony Console status code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $dsn = (string) ($input->getOption('dsn') ?: $this->runtime->auditDsn());

            return $this->writeJson($output, $this->runtime->janitorAuditGc($dsn, (int) $input->getArgument('days'), (int) $input->getArgument('batch')));
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
