<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Command;

use App\Rolling\Infrastructure\Console\Support\BenchRuntime;
use App\Rolling\Infrastructure\Console\Support\BenchStatsReport;
use App\Rolling\Infrastructure\Console\Support\BenchStatsService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:bench:report', description: 'Run synthetic benchmarks and persist a report file.')]
final class BenchReportCommand extends AbstractRoleCommand
{
    public function __construct(
        private readonly BenchRuntime $runtime,
        private readonly BenchStatsService $stats,
        private readonly BenchStatsReport $report,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('iterations', InputArgument::OPTIONAL, 'Iterations for micro benches.', '20000')
            ->addArgument('batch_n', InputArgument::OPTIONAL, 'Batch benchmark request count.', '3000')
            ->addArgument('rpc_us', InputArgument::OPTIONAL, 'Synthetic RPC latency in microseconds.', '200')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'Persist report to file.', 'var/bench_stats/bench_report.json')
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
            $report['output'] = $this->report->persist($report, (string) $input->getOption('output'));

            return $this->writeJson($output, $report);
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
