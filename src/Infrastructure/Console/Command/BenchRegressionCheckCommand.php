<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Command;

use App\Infrastructure\Console\Support\BenchRegressionComparator;
use App\Infrastructure\Console\Support\BenchRuntime;
use App\Infrastructure\Console\Support\BenchStatsReport;
use App\Infrastructure\Console\Support\BenchStatsService;
use App\Infrastructure\Console\Support\BenchThresholdEvaluator;
use App\Infrastructure\Console\Support\JsonReportLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:bench:regression-check', description: 'Run synthetic benchmarks and fail when thresholds are violated.')]
final class BenchRegressionCheckCommand extends AbstractRoleCommand
{
    public function __construct(
        private readonly BenchRuntime $runtime,
        private readonly BenchStatsService $stats,
        private readonly BenchStatsReport $report,
        private readonly BenchThresholdEvaluator $evaluator,
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
            ->addOption('max-p95-ms', null, InputOption::VALUE_REQUIRED, 'Fail when percentile p95 exceeds this value.', '5')
            ->addOption('max-p99-ms', null, InputOption::VALUE_REQUIRED, 'Fail when percentile p99 exceeds this value.', '10')
            ->addOption('max-batch-per-item-ms', null, InputOption::VALUE_REQUIRED, 'Fail when batch per-item latency exceeds this value.', '1')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'Persist report to file.')
            ->addOption('baseline', null, InputOption::VALUE_REQUIRED, 'Compare current report against a baseline JSON report.')
            ->addOption('max-p95-regression-pct', null, InputOption::VALUE_REQUIRED, 'Maximum allowed p95 regression versus baseline.', '10')
            ->addOption('max-p99-regression-pct', null, InputOption::VALUE_REQUIRED, 'Maximum allowed p99 regression versus baseline.', '10')
            ->addOption('max-batch-per-item-regression-pct', null, InputOption::VALUE_REQUIRED, 'Maximum allowed batch per-item regression versus baseline.', '10')
            ->addOption('trace', null, InputOption::VALUE_NONE, 'Include trace diagnostics.')
            ->addOption('detailed', null, InputOption::VALUE_NONE, 'Include detailed scenario payloads.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $payload = $this->runtime->run(
                (int) $input->getArgument('iterations'),
                (int) $input->getArgument('batch_n'),
                (int) $input->getArgument('rpc_us'),
            );
            $stats = $this->stats->summarize($payload);
            $report = $this->report->build($payload, $stats, (bool) $input->getOption('detailed'), (bool) $input->getOption('trace'));
            $report['gating'] = $this->evaluator->evaluate(
                $report,
                (float) $input->getOption('max-p95-ms'),
                (float) $input->getOption('max-p99-ms'),
                (float) $input->getOption('max-batch-per-item-ms'),
            );
            $baselinePath = (string) ($input->getOption('baseline') ?? '');
            if ($baselinePath !== '') {
                $baseline = $this->loader->load($baselinePath);
                $report['comparison'] = $this->comparator->compare(
                    $report,
                    $baseline,
                    (float) $input->getOption('max-p95-regression-pct'),
                    (float) $input->getOption('max-p99-regression-pct'),
                    (float) $input->getOption('max-batch-per-item-regression-pct'),
                );
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
