#!/usr/bin/env php
<?php
declare(strict_types=1);

namespace App\Bin {
    function canonScanRun(): int
    {
        $root = dirname(__DIR__);
        $src = $root . '/src';

        $forbiddenAll = [];
        $forbiddenCanonical = [];
        $legacyOnly = [];
        $externalRoots = [];
        $externalCandidates = ['Http', 'Policy', 'PolicyInterface', 'Service'];
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

                if (!$isForbidden) {
                    continue;
                }

                $forbiddenAll[] = $normalized;
                if (str_starts_with($normalized, 'src/Legacy/')) {
                    $legacyOnly[] = $normalized;
                } else {
                    $forbiddenCanonical[] = $normalized;
                }
            }
        }

        sort($externalRoots);
        sort($forbiddenAll);
        sort($forbiddenCanonical);
        sort($legacyOnly);

        $result = [
            'external_roots' => $externalRoots,
            'forbidden_directories_all' => $forbiddenAll,
            'forbidden_directory_count_all' => count($forbiddenAll),
            'forbidden_directories_canonical_placement' => $forbiddenCanonical,
            'forbidden_directory_count_canonical_placement' => count($forbiddenCanonical),
            'legacy_only_forbidden_directories' => $legacyOnly,
            'legacy_only_forbidden_directory_count' => count($legacyOnly),
        ];

        echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
        return 0;
    }
}

namespace {
    exit(\App\Bin\canonScanRun());
}
