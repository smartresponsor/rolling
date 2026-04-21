<?php

declare(strict_types=1);

namespace App\Rolling\Infrastructure\Console\Command;

use App\Rolling\Infrastructure\Console\Support\BaselineManifestManager;
use App\Rolling\Infrastructure\Console\Support\ComparisonProfileCatalog;
use App\Rolling\Infrastructure\Console\Support\JsonReportLoader;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:role:profile:report', description: 'Render a multi-profile summary from the baseline manifest and profile catalog.')]
final class ProfileReportCommand extends AbstractRoleCommand
{
    public function __construct(
        private readonly BaselineManifestManager $manifest,
        private readonly ComparisonProfileCatalog $catalog,
        private readonly JsonReportLoader $loader,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('kind', InputArgument::OPTIONAL, 'Report kind: perf, bench, or all.', 'all')
            ->addOption('manifest', null, InputOption::VALUE_REQUIRED, 'Baseline manifest path.', 'var/bench_stats/baseline_manifest.json')
            ->addOption('output', null, InputOption::VALUE_REQUIRED, 'Optional path to persist the summary JSON.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $kind = strtolower((string) $input->getArgument('kind'));
            if (!in_array($kind, ['all', 'perf', 'bench'], true)) {
                throw new \InvalidArgumentException('Kind must be all, perf, or bench.');
            }

            $manifestPath = (string) $input->getOption('manifest');
            $manifest = $this->manifest->loadManifest($manifestPath);
            $kinds = 'all' === $kind ? ['perf', 'bench'] : [$kind];
            $summary = [
                'generated_at' => gmdate(DATE_ATOM),
                'manifest' => $manifestPath,
                'kinds' => [],
            ];

            foreach ($kinds as $selectedKind) {
                $profiles = [];
                foreach ($this->catalog->profiles($selectedKind) as $profileName => $profileThresholds) {
                    $baselinePath = $this->manifest->resolveBaselinePath($manifestPath, $selectedKind, (string) $profileName);
                    $stats = null;
                    if (is_string($baselinePath) && '' !== $baselinePath && is_file($baselinePath)) {
                        $loaded = $this->loader->load($baselinePath);
                        $stats = is_array($loaded['stats'] ?? null) ? $this->normalizeStats($loaded['stats']) : null;
                    }

                    $profiles[(string) $profileName] = [
                        'thresholds' => $profileThresholds,
                        'baseline' => $baselinePath,
                        'stats' => $stats,
                    ];
                }

                $summary['kinds'][$selectedKind] = [
                    'profiles' => $profiles,
                    'manifest_entries' => array_values(array_filter(
                        is_array($manifest['baselines'] ?? null) ? $manifest['baselines'] : [],
                        static fn (mixed $entry): bool => is_array($entry) && (($entry['kind'] ?? null) === $selectedKind),
                    )),
                ];
            }

            $outputPath = (string) ($input->getOption('output') ?? '');
            if ('' !== $outputPath) {
                $directory = \dirname($outputPath);
                if (!is_dir($directory)) {
                    mkdir($directory, 0777, true);
                }
                file_put_contents($outputPath, json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
                $summary['output'] = $outputPath;
            }

            return $this->writeJson($output, $summary);
        } catch (\Throwable $throwable) {
            return $this->writeThrowable($output, $throwable);
        }
    }

    /**
     * @param array<string, mixed> $stats
     *
     * @return array<string, mixed>
     */
    private function normalizeStats(array $stats): array
    {
        foreach ($stats as $key => $value) {
            if (is_array($value)) {
                $stats[$key] = $this->normalizeStats($value);
                continue;
            }

            if (is_int($value) || is_float($value)) {
                $stats[$key] = (float) $value;
            }
        }

        return $stats;
    }
}
