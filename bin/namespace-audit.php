#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Bin {
    function namespaceAuditRun(): int
    {
        $root = dirname(__DIR__);
        $targets = [
            $root . '/src',
            $root . '/config',
            $root . '/bin',
        ];

        $files = [];
        foreach ($targets as $target) {
            if (!is_dir($target)) {
                continue;
            }

            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($target, \FilesystemIterator::SKIP_DOTS)
            );

            foreach ($iterator as $fileInfo) {
                if (!$fileInfo->isFile()) {
                    continue;
                }

                if (strtolower($fileInfo->getExtension()) !== 'php') {
                    continue;
                }

                $files[] = $fileInfo->getPathname();
            }
        }

        sort($files);

        $namespacePattern = '/^\s*namespace\s+([^;]+);/m';
        $classPattern = '/^\s*(?:final\s+|abstract\s+)?(?:class|interface|trait|enum)\s+([A-Za-z_][A-Za-z0-9_]*)/m';

        $summary = [
            'generated_at_utc' => gmdate('c'),
            'scanned_php_files' => 0,
            'files_without_namespace' => 0,
            'app_namespace_files' => 0,
            'non_app_namespace_files' => 0,
        ];

        $rootCounts = [];
        $namespaceCounts = [];
        $nonAppSamples = [];
        $noNamespaceSamples = [];

        foreach ($files as $file) {
            $relativePath = str_replace($root . '/', '', $file);
            $summary['scanned_php_files']++;

            $contents = file_get_contents($file);
            if ($contents === false) {
                continue;
            }

            if (preg_match($namespacePattern, $contents, $matches) === 1) {
                $namespace = trim($matches[1]);
                $namespaceCounts[$namespace] = ($namespaceCounts[$namespace] ?? 0) + 1;

                $rootName = explode('\\', $namespace)[0];
                $rootCounts[$rootName] = ($rootCounts[$rootName] ?? 0) + 1;

                if ($namespace === 'App' || str_starts_with($namespace, 'App\\')) {
                    $summary['app_namespace_files']++;
                } else {
                    $summary['non_app_namespace_files']++;
                    if (count($nonAppSamples) < 50) {
                        $sample = ['path' => $relativePath, 'namespace' => $namespace];
                        if (preg_match($classPattern, $contents, $classMatches) === 1) {
                            $sample['symbol'] = $classMatches[1];
                        }
                        $nonAppSamples[] = $sample;
                    }
                }
            } else {
                $summary['files_without_namespace']++;
                if (count($noNamespaceSamples) < 50) {
                    $sample = ['path' => $relativePath];
                    if (preg_match($classPattern, $contents, $classMatches) === 1) {
                        $sample['symbol'] = $classMatches[1];
                    }
                    $noNamespaceSamples[] = $sample;
                }
            }
        }

        arsort($rootCounts);
        arsort($namespaceCounts);

        $summary['namespace_roots'] = $rootCounts;
        $summary['top_namespaces'] = array_slice($namespaceCounts, 0, 40, true);
        $summary['non_app_samples'] = $nonAppSamples;
        $summary['no_namespace_samples'] = $noNamespaceSamples;

        $outputPath = $root . '/report/recovery/current-namespace-audit.json';
        file_put_contents(
            $outputPath,
            json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
        );

        echo $outputPath . PHP_EOL;
        return 0;
    }
}

namespace {
    exit(\App\Bin\namespaceAuditRun());
}
