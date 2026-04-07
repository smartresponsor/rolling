<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Command;

use App\Infrastructure\Console\Support\BaselineManifestManager;
use App\Infrastructure\Console\Support\BatchPerfRuntime;
use App\Infrastructure\Console\Support\ComparisonProfileCatalog;
use App\Infrastructure\Console\Support\JsonReportLoader;
use App\Infrastructure\Console\Support\PerfRegressionComparator;
use App\Infrastructure\Console\Support\PerfStatsReport;
use App\Infrastructure\Console\Support\PerfStatsService;
use App\Infrastructure\Console\Support\PerfThresholdEvaluator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:perf:profile-check', description: 'Run perf regression check using a named CI profile and manifest baseline.')] 
final class PerfProfileCheckCommand extends AbstractRoleCommand
{
    public function __construct(
        private readonly BatchPerfRuntime $runtime,
        private readonly PerfStatsService $stats,
        private readonly PerfStatsReport $report,
        private readonly PerfThresholdEvaluator $evaluator,
        private readonly ComparisonProfileCatalog $catalog,
        private readonly BaselineManifestManager $manifest,
        private readonly JsonReportLoader $loader,
        private readonly PerfRegressionComparator $comparator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('n', InputArgument::OPTIONAL, 'Number of requests.', '1000')
            ->addArgument('sleep_us', InputArgument::OPTIONAL, 'Optional micro-sleep in PDP.', '0')
            ->addArgument('chunk', InputArgument::OPTIONAL, 'Chunk size.', '128')
            ->addOption('profile', null, InputOption::VALUE_REQUIRED, 'CI profile name.', 'standard')
            ->addOption('manifest', null, InputOption::VALUE_REQUIRED, 'Baseline manifest path.', 'var/bench_stats/baseline_manifest.json')
            ->addOption('baseline', null, InputOption::VALUE_REQUIRED, 'Explicit baseline path override.')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'Persist report to file.')
            ->addOption('trace', null, InputOption::VALUE_NONE, 'Include trace diagnostics.')
            ->addOption('detailed', null, InputOption::VALUE_NONE, 'Include detailed runtime metrics.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $profileName = (string) $input->getOption('profile');
            $profile = $this->catalog->profile('perf', $profileName);
            $payload = $this->runtime->run(
                (int) $input->getArgument('n'),
                (int) $input->getArgument('sleep_us'),
                (int) $input->getArgument('chunk'),
            );
            $stats = $this->stats->summarize($payload);
            $report = $this->report->build($payload, $stats, (bool) $input->getOption('detailed'), (bool) $input->getOption('trace'));
            $report['profile'] = $profileName;
            $report['gating'] = $this->evaluator->evaluate(
                $report,
                (float) $profile['max-duration-ms'],
                (float) $profile['max-per-item-ms'],
                (float) $profile['min-throughput-per-sec'],
                (float) $profile['max-peak-mb'],
            );

            $baselinePath = (string) ($input->getOption('baseline') ?? '');
            if ($baselinePath === '') {
                $baselinePath = (string) ($this->manifest->resolveBaselinePath((string) $input->getOption('manifest'), 'perf', $profileName) ?? '');
            }
            if ($baselinePath !== '') {
                $baseline = $this->loader->load($baselinePath);
                $report['comparison'] = $this->comparator->compare(
                    $report,
                    $baseline,
                    (float) $profile['max-duration-regression-pct'],
                    (float) $profile['max-per-item-regression-pct'],
                    (float) $profile['max-peak-regression-pct'],
                    (float) $profile['max-throughput-drop-pct'],
                );
                $report['comparison']['baseline'] = $baselinePath;
            }

            $outputPath = (string) ($input->getOption('output') ?? '');
            if ($outputPath !== '') {
                $report['output'] = $this->report->persist($report, $outputPath);
            }

            $exitCode = (($report['gating']['ok'] ?? false) && (($report['comparison']['ok'] ?? true) === true)) ? self::SUCCESS : self::FAILURE;
            $output->writeln(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return $exitCode;
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
