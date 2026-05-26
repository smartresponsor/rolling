<?php

declare(strict_types=1);

namespace App\Rolling\Command\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAdministrationCatalogSyncServiceInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'rolling:administration:catalog:sync',
    description: 'Synchronize the Rolling administration permission catalog into Doctrine-backed ACL tables.',
)]
final class RollingAdministrationCatalogSyncCommand extends Command
{
    public function __construct(private readonly RollingAdministrationCatalogSyncServiceInterface $syncService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption(
            'bootstrap-subject',
            null,
            InputOption::VALUE_REQUIRED,
            'Optional subject identifier to grant administration.security_admin in administering:global scope.',
        );

        $this->addOption(
            'bootstrap-user-identifier',
            null,
            InputOption::VALUE_REQUIRED,
            'Optional Symfony user identifier; grants symfony:user:<identifier>.',
        );

        $this->addOption(
            'bootstrap-accessing-account-id',
            null,
            InputOption::VALUE_REQUIRED,
            'Optional Accessing account id; grants accessing:account:<id>.',
        );

        $this->addOption(
            'json',
            null,
            InputOption::VALUE_NONE,
            'Print a machine-readable JSON sync summary.',
        );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $bootstrapSubject = $input->getOption('bootstrap-subject');
        $bootstrapSubject = is_string($bootstrapSubject) ? $bootstrapSubject : null;

        $bootstrapSubjects = [];
        $bootstrapUserIdentifier = $input->getOption('bootstrap-user-identifier');
        if (is_string($bootstrapUserIdentifier) && '' !== trim($bootstrapUserIdentifier)) {
            $bootstrapSubjects[] = 'symfony:user:'.trim($bootstrapUserIdentifier);
        }

        $bootstrapAccessingAccountId = $input->getOption('bootstrap-accessing-account-id');
        if (is_string($bootstrapAccessingAccountId) && '' !== trim($bootstrapAccessingAccountId)) {
            $bootstrapSubjects[] = 'accessing:account:'.trim($bootstrapAccessingAccountId);
        }

        $result = $this->syncService->sync($bootstrapSubject, $bootstrapSubjects);

        if (true === $input->getOption('json')) {
            $output->writeln(json_encode([
                'status' => 'synchronized',
                'summary' => $result->summary(),
            ], JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR));

            return Command::SUCCESS;
        }

        $io->success('Rolling administration catalog synchronized.');
        $io->definitionList(...array_map(
            static fn (string $key, int|string|null $value): array => [$key => null === $value ? 'null' : (string) $value],
            array_keys($result->summary()),
            $result->summary(),
        ));

        return Command::SUCCESS;
    }
}
