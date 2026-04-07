<?php

declare(strict_types=1);

namespace App\Infrastructure\Console\Support;

final class BaselineManifestManager
{
    public function __construct(private readonly JsonReportLoader $loader = new JsonReportLoader())
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function loadManifest(string $path): array
    {
        if (!is_file($path) || @filesize($path) === 0) {
            return [
                'generated_at' => gmdate(DATE_ATOM),
                'baselines' => [],
            ];
        }

        $manifest = $this->loader->load($path);
        if (!isset($manifest['baselines']) || !is_array($manifest['baselines'])) {
            $manifest['baselines'] = [];
        }

        return $manifest;
    }

    /**
     * @param array<string, mixed> $manifest
     */
    public function persistManifest(array $manifest, string $path): string
    {
        $directory = \dirname($path);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $manifest['generated_at'] = gmdate(DATE_ATOM);
        file_put_contents($path, json_encode($manifest, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));

        return $path;
    }

    /**
     * @return array<string, mixed>
     */
    public function promote(string $manifestPath, string $reportPath, string $kind, string $profile, array $meta = []): array
    {
        $report = $this->loader->load($reportPath);
        $manifest = $this->loadManifest($manifestPath);
        $key = sprintf('%s:%s', $kind, $profile);
        $manifest['baselines'][$key] = [
            'kind' => $kind,
            'profile' => $profile,
            'report_path' => $reportPath,
            'promoted_at' => gmdate(DATE_ATOM),
            'report_generated_at' => (string) ($report['generated_at'] ?? ''),
            'meta' => $meta,
        ];
        $this->persistManifest($manifest, $manifestPath);

        return $manifest['baselines'][$key];
    }

    public function resolveBaselinePath(string $manifestPath, string $kind, string $profile): ?string
    {
        $manifest = $this->loadManifest($manifestPath);
        $key = sprintf('%s:%s', $kind, $profile);
        $entry = $manifest['baselines'][$key] ?? null;
        if (!is_array($entry)) {
            return null;
        }

        $path = $entry['report_path'] ?? null;
        return is_string($path) && $path != '' ? $path : null;
    }
}
