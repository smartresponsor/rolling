<?php

declare(strict_types=1);

namespace App\Rolling\Command\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAdministrationPermissionCatalogInterface;
use App\Rolling\ServiceInterface\Administration\RollingAdministrationPermissionDecisionServiceInterface;
use App\Rolling\Value\Administration\RollingAdministrationPermissionDescriptor;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'rolling:administration:permission:check',
    description: 'Check a Rolling administration permission decision for a concrete subject/scope.',
)]
final class RollingAdministrationPermissionCheckCommand extends Command
{
    public function __construct(
        private readonly RollingAdministrationPermissionDecisionServiceInterface $decisionService,
        private readonly RollingAdministrationPermissionCatalogInterface $permissionCatalog,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'subject',
                null,
                InputOption::VALUE_REQUIRED,
                'Subject identifier as seen by Administering, for example symfony:user:admin@example.com or accessing:account:123.',
            )
            ->addOption(
                'permission',
                null,
                InputOption::VALUE_REQUIRED,
                'Administration permission key to evaluate.',
            )
            ->addOption(
                'scope',
                null,
                InputOption::VALUE_REQUIRED,
                'Scope key to evaluate.',
                'administering:global',
            )
            ->addOption(
                'json',
                null,
                InputOption::VALUE_NONE,
                'Print a machine-readable JSON decision report.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $subject = $this->stringOption($input, 'subject');
        $permission = $this->stringOption($input, 'permission');
        $scope = $this->stringOption($input, 'scope');
        $scope = '' !== $scope ? $scope : 'administering:global';

        if ('' === $subject) {
            $io->error('Missing --subject. Use the exact subject identifier used by Administering.');

            return Command::INVALID;
        }

        if ('' === $permission) {
            $io->error('Missing --permission.');

            return Command::INVALID;
        }

        $catalogued = $this->permissionIsCatalogued($permission);
        $granted = $catalogued && $this->decisionService->isGranted($subject, $permission, $scope);

        $result = [
            'subject_identifier' => $subject,
            'permission' => $permission,
            'scope' => $scope,
            'catalogued' => $catalogued,
            'decision' => $granted ? 'granted' : 'denied',
            'granted' => $granted,
        ];

        if (true === $input->getOption('json')) {
            $output->writeln(json_encode($result, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

            return $granted ? Command::SUCCESS : Command::FAILURE;
        }

        $io->title('Rolling administration permission decision');
        $io->definitionList(
            ['subject' => $subject],
            ['permission' => $permission],
            ['scope' => $scope],
            ['catalogued' => $catalogued ? 'yes' : 'no'],
            ['decision' => $granted ? 'granted' : 'denied'],
        );

        if (!$catalogued) {
            $io->warning('Permission is not present in the Rolling administration catalog. Decision is fail-closed.');
        }

        return $granted ? Command::SUCCESS : Command::FAILURE;
    }

    private function stringOption(InputInterface $input, string $name): string
    {
        $value = $input->getOption($name);

        return is_string($value) ? trim($value) : '';
    }

    private function permissionIsCatalogued(string $permission): bool
    {
        foreach ($this->permissionCatalog->descriptors() as $descriptor) {
            if ($descriptor instanceof RollingAdministrationPermissionDescriptor && $descriptor->key() === $permission) {
                return true;
            }
        }

        return false;
    }
}
