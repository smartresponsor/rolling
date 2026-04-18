#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Bin {
    function autoloadContinuityAuditRun(): int
    {
        $root = dirname(__DIR__);
        $composerPath = $root . '/composer.json';
        $reportPath = $root . '/report/recovery/current-autoload-audit.json';

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
            'psr4_entries' => [],
            'classmap_entries' => [],
            'file_entries' => [],
        ];

        foreach ($psr4 as $namespace => $path) {
            $paths = is_array($path) ? $path : [$path];
            $resolved = [];
            $exists = false;

            foreach ($paths as $singlePath) {
                $singlePath = (string) $singlePath;
                $fullPath = $root . '/' . $singlePath;
                $singleExists = file_exists($fullPath);
                $exists = $exists || $singleExists;
                $resolved[] = [
                    'path' => $singlePath,
                    'exists' => $singleExists,
                ];
            }

            $summary['psr4_entries'][] = [
                'namespace' => (string) $namespace,
                'paths' => $resolved,
                'exists' => $exists,
            ];

            if (!$exists) {
                $summary['broken_entries_total']++;
                $summary['broken_entries'][] = [
                    'type' => 'psr-4',
                    'key' => (string) $namespace,
                    'paths' => array_map(static fn (array $item): string => $item['path'], $resolved),
                ];
            }
        }

        foreach ($classmap as $path) {
            $fullPath = $root . '/' . $path;
            $exists = file_exists($fullPath);

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
