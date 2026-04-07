<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Command;

use App\Infrastructure\Console\Support\RoleConsoleRuntime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:policy:migrate', description: 'Record and activate a policy migration.')]
final class PolicyMigrateCommand extends AbstractRoleCommand
{
    public function __construct(private readonly RoleConsoleRuntime $runtime)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('name', InputArgument::REQUIRED, 'Policy name.')
            ->addArgument('from', InputArgument::REQUIRED, 'From version.')
            ->addArgument('to', InputArgument::REQUIRED, 'To version.')
            ->addArgument('note', InputArgument::OPTIONAL, 'Optional note.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $name = (string) $input->getArgument('name');
            $from = (string) $input->getArgument('from');
            $to = (string) $input->getArgument('to');
            $note = $input->getArgument('note');
            $this->runtime->policyMigrate($name, $from, $to, is_string($note) ? $note : null);

            return $this->writeJson($output, [
                'ok' => true,
                'ns' => $this->runtime->rolePolicyNs(),
                'name' => $name,
                'from' => $from,
                'to' => $to,
                'note' => is_string($note) ? $note : null,
            ]);
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
