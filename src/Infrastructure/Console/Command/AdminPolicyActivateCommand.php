<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Command;

use App\Rolling\Infrastructure\Console\Support\RoleConsoleRuntime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:admin:policy:activate', description: 'Activate a policy version through the admin namespace.')]
final class AdminPolicyActivateCommand extends AbstractRoleCommand
{
    public function __construct(private readonly RoleConsoleRuntime $runtime)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Policy name.')
            ->addArgument('version', InputArgument::REQUIRED, 'Policy version.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            return $this->writeJson($output, [
                'ok' => true,
                'ns' => $this->runtime->roleAdminNs(),
                'name' => (string) $input->getArgument('name'),
                'version' => (string) $input->getArgument('version'),
                'token' => $this->runtime->policyActivate((string) $input->getArgument('name'), (string) $input->getArgument('version'), $this->runtime->roleAdminNs()),
            ]);
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
