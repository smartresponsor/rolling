<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Command;

use App\Infrastructure\Console\Support\RoleConsoleRuntime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:janitor:archive-audit', description: 'Archive old role_audit rows to JSONL and delete them.')]
final class JanitorArchiveAuditCommand extends AbstractRoleCommand
{
    public function __construct(private readonly RoleConsoleRuntime $runtime)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('days', InputArgument::OPTIONAL, 'Older-than days.', '60')
            ->addArgument('path', InputArgument::OPTIONAL, 'Archive path.', sys_get_temp_dir() . '/role_audit_archive.jsonl')
            ->addArgument('batch', InputArgument::OPTIONAL, 'Batch size.', '1000')
            ->addArgument('gzip', InputArgument::OPTIONAL, 'Whether to gzip output.', '0')
            ->addOption('dsn', null, InputOption::VALUE_REQUIRED, 'Audit DSN override.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $dsn = (string) ($input->getOption('dsn') ?: $this->runtime->auditDsn());
            return $this->writeJson($output, $this->runtime->janitorArchiveAudit(
                $dsn,
                (int) $input->getArgument('days'),
                (string) $input->getArgument('path'),
                (int) $input->getArgument('batch'),
                in_array(strtolower((string) $input->getArgument('gzip')), ['1', 'true', 'yes', 'on'], true),
            ));
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
