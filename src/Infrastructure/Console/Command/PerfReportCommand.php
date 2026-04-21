<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Command;

use App\Rolling\Infrastructure\Console\Support\BatchPerfRuntime;
use App\Rolling\Infrastructure\Console\Support\PerfStatsReport;
use App\Rolling\Infrastructure\Console\Support\PerfStatsService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:perf:report', description: 'Run perf benchmark and persist a report file.')]
final class PerfReportCommand extends AbstractRoleCommand
{
    public function __construct(
        private readonly BatchPerfRuntime $runtime,
        private readonly PerfStatsService $stats,
        private readonly PerfStatsReport $report,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('n', InputArgument::OPTIONAL, 'Number of requests.', '1000')
            ->addArgument('sleep_us', InputArgument::OPTIONAL, 'Optional micro-sleep in PDP.', '0')
            ->addArgument('chunk', InputArgument::OPTIONAL, 'Chunk size.', '128')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'Persist report to file.', 'var/bench_stats/perf_report.json')
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
            $report['output'] = $this->report->persist($report, (string) $input->getOption('output'));

            return $this->writeJson($output, $report);
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
