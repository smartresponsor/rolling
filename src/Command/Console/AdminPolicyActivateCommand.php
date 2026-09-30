<?php

declare(strict_types=1);

namespace App\Rolling\Command\Console;

use App\Rolling\Infrastructure\Console\Support\RoleConsoleRuntime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:admin:policy:activate', description: 'Activate a policy version through the admin namespace.')]
/**
 * Implements the AdminPolicyActivateCommand console workflow.
 */
final class AdminPolicyActivateCommand extends AbstractRoleCommand
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
            ->addArgument('nameEntity', InputArgument::REQUIRED, 'Policy nameEntity.')
            ->addArgument('version', InputArgument::REQUIRED, 'Policy version.');
    }

    /**
     * Execute the command and return a Symfony Console status code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            return $this->writeJson($output, [
                'ok' => true,
                'ns' => $this->runtime->roleAdminNs(),
                'nameEntity' => (string) $input->getArgument('nameEntity'),
                'version' => (string) $input->getArgument('version'),
                'token' => $this->runtime->policyActivate((string) $input->getArgument('nameEntity'), (string) $input->getArgument('version'), $this->runtime->roleAdminNs()),
            ]);
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
