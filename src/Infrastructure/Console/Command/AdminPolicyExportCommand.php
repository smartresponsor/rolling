<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Command;

use App\Rolling\Infrastructure\Console\Support\RoleConsoleRuntime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:admin:policy:export', description: 'Export a policy document through the admin namespace.')]
final class AdminPolicyExportCommand extends AbstractRoleCommand
{
    public function __construct(private readonly RoleConsoleRuntime $runtime)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('nameEntity', InputArgument::REQUIRED, 'Policy nameEntity.')
            ->addArgument('version', InputArgument::REQUIRED, 'Policy version.')
            ->addArgument('out', InputArgument::OPTIONAL, 'Optional output path.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $nameEntity = (string) $input->getArgument('nameEntity');
            $version = (string) $input->getArgument('version');
            $doc = $this->runtime->policyExport($nameEntity, $version, $this->runtime->roleAdminNs());
            if (null === $doc) {
                return $this->writeJson($output, [
                    'ok' => false,
                    'ns' => $this->runtime->roleAdminNs(),
                    'nameEntity' => $nameEntity,
                    'version' => $version,
                    'error' => 'not found',
                ]);
            }

            $outPath = $input->getArgument('out');
            if (is_string($outPath) && '' !== $outPath) {
                file_put_contents($outPath, $doc);
            }

            return $this->writeJson($output, [
                'ok' => true,
                'ns' => $this->runtime->roleAdminNs(),
                'nameEntity' => $nameEntity,
                'version' => $version,
                'out' => (is_string($outPath) && '' !== $outPath) ? $outPath : null,
                'document' => (is_string($outPath) && '' !== $outPath) ? null : $doc,
            ]);
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
