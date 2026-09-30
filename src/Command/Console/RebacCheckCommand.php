<?php

declare(strict_types=1);

namespace App\Rolling\Command\Console;

use App\Rolling\Infrastructure\Console\Support\RoleConsoleRuntime;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:rebac:check', description: 'Check a REBAC relation for subject/object/relation triple.')]
/**
 * Implements the RebacCheckCommand console workflow.
 */
final class RebacCheckCommand extends AbstractRoleCommand
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
            ->addArgument('subject', InputArgument::REQUIRED, 'Subject in type:id form.')
            ->addArgument('object', InputArgument::REQUIRED, 'Object in type:id form.')
            ->addArgument('relation', InputArgument::REQUIRED, 'Relation nameEntity.');
    }

    /**
     * Execute the command and return a Symfony Console status code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $result = $this->runtime->rebacChecker()->check(
                $this->runtime->roleRebacNs(),
                (string) $input->getArgument('subject'),
                (string) $input->getArgument('object'),
                (string) $input->getArgument('relation'),
            );
            $result['ns'] = $this->runtime->roleRebacNs();
            $this->writeJson($output, $result);

            return ($result['allow'] ?? false) ? self::SUCCESS : self::FAILURE;
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
