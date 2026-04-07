<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Command;

use App\Infrastructure\Console\Support\RoleConsoleRuntime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:rebac:write', description: 'Write a direct REBAC tuple.')]
final class RebacWriteCommand extends AbstractRoleCommand
{
    public function __construct(private readonly RoleConsoleRuntime $runtime)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('objectType', InputArgument::REQUIRED, 'Object type.')
            ->addArgument('objectId', InputArgument::REQUIRED, 'Object id.')
            ->addArgument('relation', InputArgument::REQUIRED, 'Relation name.')
            ->addArgument('subjectType', InputArgument::REQUIRED, 'Subject type.')
            ->addArgument('subjectId', InputArgument::REQUIRED, 'Subject id.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $token = $this->runtime->rebacWriter()->write(
                $this->runtime->roleRebacNs(),
                [
                    $this->runtime->rebacTuple(
                        (string) $input->getArgument('objectType'),
                        (string) $input->getArgument('objectId'),
                        (string) $input->getArgument('relation'),
                        (string) $input->getArgument('subjectType'),
                        (string) $input->getArgument('subjectId'),
                    ),
                ],
            );

            return $this->writeJson($output, [
                'ok' => true,
                'ns' => $this->runtime->roleRebacNs(),
                'rev' => (string) $token,
            ]);
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
