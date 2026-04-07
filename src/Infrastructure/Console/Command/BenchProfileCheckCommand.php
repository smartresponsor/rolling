<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Command;

use App\Infrastructure\Console\Support\BaselineManifestManager;
use App\Infrastructure\Console\Support\BenchRegressionComparator;
use App\Infrastructure\Console\Support\BenchRuntime;
use App\Infrastructure\Console\Support\BenchStatsReport;
use App\Infrastructure\Console\Support\BenchStatsService;
use App\Infrastructure\Console\Support\BenchThresholdEvaluator;
use App\Infrastructure\Console\Support\ComparisonProfileCatalog;
use App\Infrastructure\Console\Support\JsonReportLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:bench:profile-check', description: 'Run bench regression check using a named CI profile and manifest baseline.')] 
final class BenchProfileCheckCommand extends AbstractRoleCommand
{
    public function __construct(
        private readonly BenchRuntime $runtime,
        private readonly BenchStatsService $stats,
        private readonly BenchStatsReport $report,
        private readonly BenchThresholdEvaluator $evaluator,
        private readonly ComparisonProfileCatalog $catalog,
        private readonly BaselineManifestManager $manifest,
        private readonly JsonReportLoader $loader,
        private readonly BenchRegressionComparator $comparator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('iterations', InputArgument::OPTIONAL, 'Iterations for micro benches.', '20000')
            ->addArgument('batch_n', InputArgument::OPTIONAL, 'Batch benchmark request count.', '3000')
            ->addArgument('rpc_us', InputArgument::OPTIONAL, 'Synthetic RPC latency in microseconds.', '200')
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
            $profile = $this->catalog->profile('bench', $profileName);
            $payload = $this->runtime->run(
                (int) $input->getArgument('iterations'),
                (int) $input->getArgument('batch_n'),
                (int) $input->getArgument('rpc_us'),
            );
            $stats = $this->stats->summarize($payload);
            $report = $this->report->build($payload, $stats, (bool) $input->getOption('detailed'), (bool) $input->getOption('trace'));
            $report['profile'] = $profileName;
            $report['gating'] = $this->evaluator->evaluate(
                $report,
                (float) $profile['max-p95-ms'],
                (float) $profile['max-p99-ms'],
                (float) $profile['max-batch-per-item-ms'],
            );

            $baselinePath = (string) ($input->getOption('baseline') ?? '');
            if ($baselinePath === '') {
                $baselinePath = (string) ($this->manifest->resolveBaselinePath((string) $input->getOption('manifest'), 'bench', $profileName) ?? '');
            }
            if ($baselinePath !== '') {
                $baseline = $this->loader->load($baselinePath);
                $report['comparison'] = $this->comparator->compare(
                    $report,
                    $baseline,
                    (float) $profile['max-p95-regression-pct'],
                    (float) $profile['max-p99-regression-pct'],
                    (float) $profile['max-batch-per-item-regression-pct'],
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
