#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Rolling\Bin {
    function canonScanRun(): int
    {
        $root = dirname(__DIR__);
        $src = $root . '/src';

        $forbiddenDirectories = [];
        $externalRoots = [];
        $externalCandidates = ['Http', 'Policy', 'PolicyInterface', 'Service', 'tool', 'test', 'main'];
        foreach ($externalCandidates as $candidate) {
            if (is_dir($root . '/' . $candidate)) {
                $externalRoots[] = $candidate;
            }
        }

        if (is_dir($src)) {
            $iterator = new \RecursiveIteratorIterator(
                new \RecursiveDirectoryIterator($src, \FilesystemIterator::SKIP_DOTS),
                \RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $item) {
                if (!$item->isDir()) {
                    continue;
                }

                $path = str_replace($root . DIRECTORY_SEPARATOR, '', $item->getPathname());
                $normalized = str_replace('\\', '/', $path);
                $isEntityScopedRole = preg_match('#^src/Entity/Role($|/)#', $normalized) === 1;
                $isForbidden =
                    preg_match('#(^|/)Domain($|/)#', $normalized) ||
                    preg_match('#(^|/)Port($|/)#', $normalized) ||
                    preg_match('#(^|/)(Adapter|Adaptor)($|/)#', $normalized) ||
                    preg_match('#(^|/)Role($|/)#', $normalized) ||
                    preg_match('#(^|/)Rolling($|/)#', $normalized);

                if ($isEntityScopedRole) {
                    $isForbidden = false;
                }

                if ($isForbidden) {
                    $forbiddenDirectories[] = $normalized;
                }
            }
        }

        sort($externalRoots);
        sort($forbiddenDirectories);

        $result = [
            'generated_at_utc' => gmdate('c'),
            'external_roots' => $externalRoots,
            'external_root_count' => count($externalRoots),
            'forbidden_directories' => $forbiddenDirectories,
            'forbidden_directory_count' => count($forbiddenDirectories),
        ];

        $outputPath = $root . '/report/recovery/current-canon-scan.json';
        file_put_contents($outputPath, json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
        echo $outputPath . PHP_EOL;
        return 0;
    }
}

namespace {
    exit(\App\Rolling\Bin\canonScanRun());
}
