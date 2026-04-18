#!/usr/bin/env php
<?php
declare(strict_types=1);

namespace App\Bin {
    function autoloadContinuityAuditRun(): int
    {
        $root = dirname(__DIR__);
        $composerPath = $root . '/composer.json';
        $reportPath = $root . '/report/rolling-role-w12-autoload-audit.json';

        $composerJson = file_get_contents($composerPath);
        if ($composerJson === false) {
            fwrite(STDERR, "Unable to read composer.json" . PHP_EOL);
            return 1;
        }

        $composer = json_decode($composerJson, true);
        if (!is_array($composer)) {
            fwrite(STDERR, "Invalid composer.json" . PHP_EOL);
            return 1;
        }

        $autoload = $composer['autoload'] ?? [];
        $psr4 = is_array($autoload['psr-4'] ?? null) ? $autoload['psr-4'] : [];
        $classmap = is_array($autoload['classmap'] ?? null) ? $autoload['classmap'] : [];
        $files = is_array($autoload['files'] ?? null) ? $autoload['files'] : [];

        $summary = [
            'generated_at_utc' => gmdate('c'),
            'psr4_entries_total' => count($psr4),
            'classmap_entries_total' => count($classmap),
            'files_entries_total' => count($files),
            'broken_entries_total' => 0,
            'broken_entries' => [],
            'legacy_psr4_entries_total' => 0,
            'legacy_classmap_entries_total' => 0,
            'legacy_files_entries_total' => 0,
            'sdk_mapping' => null,
            'app_role_psr4_legacy_groups' => [],
            'classmap_entries' => [],
            'file_entries' => [],
        ];

        foreach ($psr4 as $namespace => $path) {
            $fullPath = $root . '/' . $path;
            $exists = file_exists($fullPath);
            $isLegacy = str_starts_with($path, 'src/Legacy/');

            if ($isLegacy) {
                $summary['legacy_psr4_entries_total']++;
            }

            if ($namespace === 'SmartResponsor\\RoleSdk\\V2\\') {
                $summary['sdk_mapping'] = [
                    'namespace' => $namespace,
                    'path' => $path,
                    'exists' => $exists,
                ];
            }

            if (str_starts_with($namespace, 'App\\') && str_contains($namespace, '\\Role\\') && $isLegacy) {
                $summary['app_role_psr4_legacy_groups'][$namespace] = $path;
            }

            if (!$exists) {
                $summary['broken_entries_total']++;
                $summary['broken_entries'][] = [
                    'type' => 'psr-4',
                    'key' => $namespace,
                    'path' => $path,
                ];
            }
        }

        foreach ($classmap as $path) {
            $fullPath = $root . '/' . $path;
            $exists = file_exists($fullPath);
            $isLegacy = str_starts_with($path, 'src/Legacy/');

            if ($isLegacy) {
                $summary['legacy_classmap_entries_total']++;
            }

            $summary['classmap_entries'][] = [
                'path' => $path,
                'exists' => $exists,
            ];

            if (!$exists) {
                $summary['broken_entries_total']++;
                $summary['broken_entries'][] = [
                    'type' => 'classmap',
                    'path' => $path,
                ];
            }
        }

        foreach ($files as $path) {
            $fullPath = $root . '/' . $path;
            $exists = file_exists($fullPath);
            $isLegacy = str_starts_with($path, 'src/Legacy/');

            if ($isLegacy) {
                $summary['legacy_files_entries_total']++;
            }

            $summary['file_entries'][] = [
                'path' => $path,
                'exists' => $exists,
            ];

            if (!$exists) {
                $summary['broken_entries_total']++;
                $summary['broken_entries'][] = [
                    'type' => 'files',
                    'path' => $path,
                ];
            }
        }

        ksort($summary['app_role_psr4_legacy_groups']);

        file_put_contents(
            $reportPath,
            json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
        );

        echo $reportPath . PHP_EOL;
        return 0;
    }
}

namespace {
    exit(\App\Bin\autoloadContinuityAuditRun());
}
