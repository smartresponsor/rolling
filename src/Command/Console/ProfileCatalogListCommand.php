<?php

declare(strict_types=1);

namespace App\Rolling\Command\Console;

use App\Rolling\Infrastructure\Console\Support\ComparisonProfileCatalog;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:profile:list', description: 'List CI comparison profiles for perf or bench.')]
/**
 * Implements the ProfileCatalogListCommand console workflow.
 */
final class ProfileCatalogListCommand extends AbstractRoleCommand
{
    public function __construct(private readonly ComparisonProfileCatalog $catalog)
    {
        parent::__construct();
    }

    /**
     * Configure command arguments and options.
     */
    protected function configure(): void
    {
        $this->addArgument('kind', InputArgument::OPTIONAL, 'Profile kind: perf or bench.', 'perf');
    }

    /**
     * Execute the command and return a Symfony Console status code.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $kind = (string) $input->getArgument('kind');
            $payload = [
                'kind' => $kind,
                'profiles' => $this->catalog->profile($kind, 'smoke') ? $this->catalog->all()[$kind] : [],
            ];
            $output->writeln(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

            return self::SUCCESS;
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
