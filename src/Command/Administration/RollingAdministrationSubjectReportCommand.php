<?php

declare(strict_types=1);

namespace App\Rolling\Command\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAdministrationSubjectAccessReportProviderInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'rolling:administration:subject:report',
    description: 'Show the Rolling administration roles, rules, and effective permissions for a subject.',
)]
final class RollingAdministrationSubjectReportCommand extends Command
{
    public function __construct(private readonly RollingAdministrationSubjectAccessReportProviderInterface $reportProvider)
    {
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
                'Print a machine-readable JSON report instead of operator tables.',
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $subject = $this->stringOption($input, 'subject');
        $scope = $this->stringOption($input, 'scope');
        $scope = '' !== $scope ? $scope : 'administering:global';

        if ('' === $subject) {
            $io->error('Missing --subject. Use the exact subject identifier used by Administering.');

            return Command::INVALID;
        }

        $report = $this->reportProvider->reportFor($subject, $scope);

        if (true === $input->getOption('json')) {
            $json = json_encode($report->toArray(), JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
            $output->writeln($json);

            return Command::SUCCESS;
        }

        $io->title('Rolling administration subject report');
        $io->definitionList(
            ['subject' => $report->subjectIdentifier()],
            ['scope' => $report->scope()],
            ['assigned roles' => (string) count($report->assignedRoles())],
            ['effective roles' => (string) count($report->effectiveRoles())],
            ['granted permissions' => (string) count($report->grantedPermissions())],
            ['denied permissions' => (string) count($report->deniedPermissions())],
        );

        $this->renderAssignedRoles($io, $report->assignedRoles());
        $this->renderEffectiveRoles($io, $report->effectiveRoles());
        $this->renderDirectRules($io, $report->directRules());
        $this->renderRolePermissions($io, $report->rolePermissions());
        $this->renderPermissionList($io, 'Granted permissions', $report->grantedPermissions(), 'No permissions granted for this subject/scope.');
        $this->renderPermissionList($io, 'Denied permissions', $report->deniedPermissions(), 'No denied catalog permissions for this subject/scope.');
        $this->renderPermissionList($io, 'Catalogued permissions', $report->cataloguedPermissions(), 'No catalogued administration permissions found.');

        return Command::SUCCESS;
    }

    private function stringOption(InputInterface $input, string $name): string
    {
        $value = $input->getOption($name);

        return is_string($value) ? trim($value) : '';
    }

    /** @param list<array<string, mixed>> $rows */
    private function renderAssignedRoles(SymfonyStyle $io, array $rows): void
    {
        $io->section('Assigned roles');
        if ([] === $rows) {
            $io->writeln('No matching subject role assignments found.');

            return;
        }

        $io->table(
            ['role', 'scope', 'matches', 'enabled', 'assigned at'],
            array_map(static fn (array $row): array => [
                (string) ($row['role_key'] ?? ''),
                (string) ($row['scope_key'] ?? ''),
                true === ($row['matches_requested_scope'] ?? false) ? 'yes' : 'no',
                true === ($row['role_enabled'] ?? false) ? 'yes' : 'no',
                (string) ($row['assigned_at'] ?? ''),
            ], $rows),
        );
    }

    /** @param list<string> $roles */
    private function renderEffectiveRoles(SymfonyStyle $io, array $roles): void
    {
        $io->section('Effective roles');
        if ([] === $roles) {
            $io->writeln('No effective roles for this subject/scope.');

            return;
        }

        $io->listing($roles);
    }

    /** @param list<array<string, mixed>> $rows */
    private function renderDirectRules(SymfonyStyle $io, array $rows): void
    {
        $io->section('Direct ACL rules');
        if ([] === $rows) {
            $io->writeln('No direct ACL rules found for this subject.');

            return;
        }

        $io->table(
            ['permission', 'scope', 'effect', 'enabled', 'matches'],
            array_map(static fn (array $row): array => [
                (string) ($row['permission_key'] ?? ''),
                (string) ($row['scope_key'] ?? ''),
                (string) ($row['effect'] ?? ''),
                true === ($row['enabled'] ?? false) ? 'yes' : 'no',
                true === ($row['matches_requested_scope'] ?? false) ? 'yes' : 'no',
            ], $rows),
        );
    }

    /** @param list<array<string, mixed>> $rows */
    private function renderRolePermissions(SymfonyStyle $io, array $rows): void
    {
        $io->section('Role permissions');
        if ([] === $rows) {
            $io->writeln('No role permissions found through effective roles.');

            return;
        }

        $io->table(
            ['role', 'permission', 'scope pattern', 'effect', 'matches'],
            array_map(static fn (array $row): array => [
                (string) ($row['role_key'] ?? ''),
                (string) ($row['permission_key'] ?? ''),
                (string) ($row['scope_pattern'] ?? ''),
                (string) ($row['effect'] ?? ''),
                true === ($row['matches_requested_scope'] ?? false) ? 'yes' : 'no',
            ], $rows),
        );
    }

    /** @param list<string> $permissions */
    private function renderPermissionList(SymfonyStyle $io, string $title, array $permissions, string $emptyMessage): void
    {
        $io->section($title);
        if ([] === $permissions) {
            $io->writeln($emptyMessage);

            return;
        }

        $io->listing($permissions);
    }
}
