<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Command;

use App\Rolling\Infrastructure\Console\Support\RoleConsoleRuntime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:policy:import', description: 'Import a policy JSON document into the policy registry.')]
final class PolicyImportCommand extends AbstractRoleCommand
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
            if (false === $doc) {
                throw new \RuntimeException(sprintf('cannot read %s', $file));
            }

            return $this->writeJson($output, [
                'ok' => true,
                'ns' => $this->runtime->rolePolicyNs(),
                'name' => (string) $input->getArgument('name'),
                'version' => (string) $input->getArgument('version'),
                'token' => $this->runtime->policyImport((string) $input->getArgument('name'), (string) $input->getArgument('version'), $doc),
            ]);
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
