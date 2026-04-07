<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Command;

use App\Infrastructure\Console\Support\BaselineManifestManager;
use App\Infrastructure\Console\Support\JsonReportLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:profile:promote', description: 'Promote a known-good perf or bench report into the baseline manifest using a named profile.')]
final class ProfileAutoPromoteCommand extends AbstractRoleCommand
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
            ->addArgument('kind', InputArgument::REQUIRED, 'Report kind: perf or bench.')
            ->addArgument('report', InputArgument::REQUIRED, 'Path to a known-good perf/bench report JSON.')
            ->addOption('profile', null, InputOption::VALUE_REQUIRED, 'Baseline profile name. Defaults to report profile or standard.', '')
            ->addOption('manifest', null, InputOption::VALUE_REQUIRED, 'Baseline manifest path.', 'var/bench_stats/baseline_manifest.json')
            ->addOption('label', null, InputOption::VALUE_REQUIRED, 'Optional label recorded in manifest.', '')
            ->addOption('require-passing', null, InputOption::VALUE_NONE, 'Require report gating/comparison to be passing before promote.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $kind = strtolower(trim((string) $input->getArgument('kind')));
            if (!in_array($kind, ['perf', 'bench'], true)) {
                throw new \InvalidArgumentException('Kind must be perf or bench.');
            }

            $reportPath = (string) $input->getArgument('report');
            $report = $this->loader->load($reportPath);
            if (($report['kind'] ?? null) !== $kind) {
                throw new \RuntimeException(sprintf('Provided report is not a %s report.', $kind));
            }

            $profile = trim((string) $input->getOption('profile'));
            if ($profile === '') {
                $profile = (string) ($report['profile'] ?? 'standard');
            }

            if ((bool) $input->getOption('require-passing')) {
                $gatingOk = (($report['gating']['ok'] ?? true) === true);
                $comparisonOk = (($report['comparison']['ok'] ?? true) === true);
                if (!$gatingOk || !$comparisonOk) {
                    throw new \RuntimeException('Refusing to promote a report with failing gating/comparison state.');
                }
            }

            $entry = $this->manifest->promote(
                (string) $input->getOption('manifest'),
                $reportPath,
                $kind,
                $profile,
                ['label' => (string) $input->getOption('label')],
            );

            return $this->writeJson($output, [
                'ok' => true,
                'kind' => $kind,
                'profile' => $profile,
                'baseline' => $entry,
            ]);
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }
}
