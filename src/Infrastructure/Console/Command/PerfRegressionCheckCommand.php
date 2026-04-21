<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Command;

use App\Rolling\Infrastructure\Console\Support\BatchPerfRuntime;
use App\Rolling\Infrastructure\Console\Support\JsonReportLoader;
use App\Rolling\Infrastructure\Console\Support\PerfRegressionComparator;
use App\Rolling\Infrastructure\Console\Support\PerfStatsReport;
use App\Rolling\Infrastructure\Console\Support\PerfStatsService;
use App\Rolling\Infrastructure\Console\Support\PerfThresholdEvaluator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:perf:regression-check', description: 'Run perf benchmark and fail when thresholds are violated.')]
final class PerfRegressionCheckCommand extends AbstractRoleCommand
{
    public function __construct(
        private readonly BatchPerfRuntime $runtime,
        private readonly PerfStatsService $stats,
        private readonly PerfStatsReport $report,
        private readonly PerfThresholdEvaluator $evaluator,
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
            ->addOption('max-duration-ms', null, InputOption::VALUE_REQUIRED, 'Fail when duration exceeds this value.', '1000')
            ->addOption('max-per-item-ms', null, InputOption::VALUE_REQUIRED, 'Fail when per-item latency exceeds this value.', '1')
            ->addOption('min-throughput-per-sec', null, InputOption::VALUE_REQUIRED, 'Fail when throughput drops below this value.', '500')
            ->addOption('max-peak-mb', null, InputOption::VALUE_REQUIRED, 'Fail when memory peak exceeds this value.', '256')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'Persist report to file.')
            ->addOption('baseline', null, InputOption::VALUE_REQUIRED, 'Compare current report against a baseline JSON report.')
            ->addOption('max-duration-regression-pct', null, InputOption::VALUE_REQUIRED, 'Maximum allowed duration regression versus baseline.', '10')
            ->addOption('max-per-item-regression-pct', null, InputOption::VALUE_REQUIRED, 'Maximum allowed per-item regression versus baseline.', '10')
            ->addOption('max-peak-regression-pct', null, InputOption::VALUE_REQUIRED, 'Maximum allowed peak memory regression versus baseline.', '10')
            ->addOption('max-throughput-drop-pct', null, InputOption::VALUE_REQUIRED, 'Maximum allowed throughput drop versus baseline.', '10')
            ->addOption('trace', null, InputOption::VALUE_NONE, 'Include trace diagnostics.')
            ->addOption('detailed', null, InputOption::VALUE_NONE, 'Include detailed runtime metrics.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $payload = $this->runtime->run(
                (int) $input->getArgument('n'),
                (int) $input->getArgument('sleep_us'),
                (int) $input->getArgument('chunk'),
            );
            $stats = $this->stats->summarize($payload);
            $report = $this->report->build($payload, $stats, (bool) $input->getOption('detailed'), (bool) $input->getOption('trace'));
            $report['gating'] = $this->evaluator->evaluate(
                $report,
                (float) $input->getOption('max-duration-ms'),
                (float) $input->getOption('max-per-item-ms'),
                (float) $input->getOption('min-throughput-per-sec'),
                (float) $input->getOption('max-peak-mb'),
            );
            $baselinePath = (string) ($input->getOption('baseline') ?? '');
            if ('' !== $baselinePath) {
                $baseline = $this->loader->load($baselinePath);
                $report['comparison'] = $this->comparator->compare(
                    $report,
                    $baseline,
                    (float) $input->getOption('max-duration-regression-pct'),
                    (float) $input->getOption('max-per-item-regression-pct'),
                    (float) $input->getOption('max-peak-regression-pct'),
                    (float) $input->getOption('max-throughput-drop-pct'),
                );
            }
            $outputPath = (string) ($input->getOption('output') ?? '');
            if ('' !== $outputPath) {
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
