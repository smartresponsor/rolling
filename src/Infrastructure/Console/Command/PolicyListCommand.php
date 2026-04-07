<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Command;

use App\Infrastructure\Console\Support\RoleConsoleRuntime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:policy:list', description: 'List policy versions for a policy name.')]
final class PolicyListCommand extends AbstractRoleCommand
{
    public function __construct(private readonly RoleConsoleRuntime $runtime)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addArgument('name', InputArgument::REQUIRED, 'Policy name.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $name = (string) $input->getArgument('name');
            $records = $this->runtime->policyService()->listVersions($this->runtime->rolePolicyNs(), $name);
            $payload = [
                'ok' => true,
                'ns' => $this->runtime->rolePolicyNs(),
                'name' => $name,
                'versions' => array_map(static fn (object $record): array => [
                    'ns' => $record->ns,
                    'name' => $record->name,
                    'version' => $record->version,
                    'is_active' => $record->isActive,
                    'created_at' => $record->createdAt,
                ], $records),
            ];

            return $this->writeJson($output, $payload);
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
