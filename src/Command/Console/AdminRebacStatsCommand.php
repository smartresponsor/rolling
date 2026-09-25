<?php

declare(strict_types=1);

namespace App\Rolling\Command\Console;

use App\Rolling\Infrastructure\Console\Support\RoleConsoleRuntime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:admin:rebac:stats', description: 'Print REBAC stats for the configured admin namespace.')]
/**
 * Implements the AdminRebacStatsCommand console workflow.
 */
final class AdminRebacStatsCommand extends AbstractRoleCommand
{
    public function __construct(private readonly RoleConsoleRuntime $runtime)
    {
        parent::__construct();
    }

    /**
     * Execute the command and return a Symfony Console status code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            return $this->writeJson($output, $this->runtime->adminRebacStatsService()->stats($this->runtime->roleAdminNs()));
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
