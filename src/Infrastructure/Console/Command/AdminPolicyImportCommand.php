<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Command;

use App\Infrastructure\Console\Support\RoleConsoleRuntime;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:admin:policy:import', description: 'Import a policy document through the admin namespace.')]
final class AdminPolicyImportCommand extends AbstractRoleCommand
{
    public function __construct(private readonly RoleConsoleRuntime $runtime)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Policy name.')
            ->addArgument('version', InputArgument::REQUIRED, 'Policy version.')
            ->addArgument('file', InputArgument::REQUIRED, 'Path to JSON document.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $file = (string) $input->getArgument('file');
            $doc = @file_get_contents($file);
            if ($doc === false) {
                throw new RuntimeException(sprintf('cannot read %s', $file));
            }

            return $this->writeJson($output, [
                'ok' => true,
                'ns' => $this->runtime->roleAdminNs(),
                'name' => (string) $input->getArgument('name'),
                'version' => (string) $input->getArgument('version'),
                'token' => $this->runtime->policyImport((string) $input->getArgument('name'), (string) $input->getArgument('version'), $doc, $this->runtime->roleAdminNs()),
            ]);
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
