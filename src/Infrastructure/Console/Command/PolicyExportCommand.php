<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Command;

use App\Infrastructure\Console\Support\RoleConsoleRuntime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:policy:export', description: 'Export a policy JSON document from the policy registry.')]
final class PolicyExportCommand extends AbstractRoleCommand
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
            ->addArgument('out', InputArgument::OPTIONAL, 'Optional output path.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $name = (string) $input->getArgument('name');
            $version = (string) $input->getArgument('version');
            $doc = $this->runtime->policyExport($name, $version);
            if ($doc === null) {
                return $this->writeJson($output, [
                    'ok' => false,
                    'ns' => $this->runtime->rolePolicyNs(),
                    'name' => $name,
                    'version' => $version,
                    'error' => 'not found',
                ]);
            }

            $outPath = $input->getArgument('out');
            if (is_string($outPath) && $outPath !== '') {
                file_put_contents($outPath, $doc);
            }

            return $this->writeJson($output, [
                'ok' => true,
                'ns' => $this->runtime->rolePolicyNs(),
                'name' => $name,
                'version' => $version,
                'out' => (is_string($outPath) && $outPath !== '') ? $outPath : null,
                'document' => (is_string($outPath) && $outPath !== '') ? null : $doc,
            ]);
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
