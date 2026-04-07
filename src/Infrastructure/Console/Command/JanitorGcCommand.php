<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Command;

use App\Infrastructure\Console\Support\RoleConsoleRuntime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:janitor:gc', description: 'Run audit/replay janitor using retention config.')]
final class JanitorGcCommand extends AbstractRoleCommand
{
    public function __construct(private readonly RoleConsoleRuntime $runtime)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('dsn', null, InputOption::VALUE_REQUIRED, 'Audit DSN override.')
            ->addOption('config', null, InputOption::VALUE_REQUIRED, 'Retention config path override.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $dsn = (string) ($input->getOption('dsn') ?: $this->runtime->auditDsn());
            $config = (string) ($input->getOption('config') ?: $this->runtime->retentionConfigPath());
            $result = $this->runtime->janitor($dsn, $config)->run();
            $result['dsn'] = $dsn;
            $result['config'] = $config;

            return $this->writeJson($output, $result);
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
