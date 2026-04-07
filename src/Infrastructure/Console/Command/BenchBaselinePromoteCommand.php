<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Command;

use App\Infrastructure\Console\Support\BaselineManifestManager;
use App\Infrastructure\Console\Support\JsonReportLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:bench:baseline:promote', description: 'Promote a known-good bench report into the baseline manifest.')] 
final class BenchBaselinePromoteCommand extends AbstractRoleCommand
{
    public function __construct(
        private readonly BaselineManifestManager $manifest,
        private readonly JsonReportLoader $loader,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('report', InputArgument::REQUIRED, 'Path to a known-good bench report JSON.')
            ->addOption('profile', null, InputOption::VALUE_REQUIRED, 'Baseline profile name.', 'standard')
            ->addOption('manifest', null, InputOption::VALUE_REQUIRED, 'Baseline manifest path.', 'var/bench_stats/baseline_manifest.json')
            ->addOption('label', null, InputOption::VALUE_REQUIRED, 'Optional label recorded in manifest.', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $reportPath = (string) $input->getArgument('report');
            $report = $this->loader->load($reportPath);
            if (($report['kind'] ?? null) !== 'bench') {
                throw new \RuntimeException('Provided report is not a bench report.');
            }

            $entry = $this->manifest->promote(
                (string) $input->getOption('manifest'),
                $reportPath,
                'bench',
                (string) $input->getOption('profile'),
                ['label' => (string) $input->getOption('label')],
            );
            $output->writeln(json_encode(['ok' => true, 'baseline' => $entry], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return self::SUCCESS;
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
