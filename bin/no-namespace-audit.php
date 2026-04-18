#!/usr/bin/env php
<?php
declare(strict_types=1);

namespace App\Bin {
    function noNamespaceAuditRun(): int
    {
        $root = dirname(__DIR__);
        $allowlist = [
            'bin/php-lint-all.php',
            'bin/php82-cs-fixer.php',
            'bin/role-admin.php',
            'bin/role-batch-perf.php',
            'bin/role-bench.php',
            'bin/role-janitor.php',
            'bin/role-policy.php',
            'bin/role-rebac.php',
            'tests/Fixture/Role/deny-by-revocation.php',
            'tests/Fixture/Role/elimination-cascade.php',
            'tests/Fixture/Role/multi-hop-chain.php',
            'tests/Fixture/Role/multi-tenant-isolation.php',
            'tests/Fixture/Role/partial-propagation.php',
            'tests/Fixture/Role/propagation-chain.php',
            'tests/Fixture/Role/relation-override.php',
            'tests/Fixture/Role/revoke-after-propagation.php',
            'tests/Fixture/Role/tenant-basic.php',
        ];

        $targets = [$root . '/bin', $root . '/src', $root . '/tests'];
        $namespacePattern = '/^\s*namespace\s+[^;]+;/m';
        $results = [
            'generated_at_utc' => gmdate('c'),
            'allowlist' => $allowlist,
            'total_no_namespace_files' => 0,
            'intentional_no_namespace_files' => [],
            'unexpected_no_namespace_files' => [],
        ];

        foreach ($targets as $target) {
            if (!is_dir($target)) {
                continue;
            }
            $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($target, \FilesystemIterator::SKIP_DOTS));
            foreach ($iterator as $fileInfo) {
                if (!$fileInfo->isFile() || strtolower($fileInfo->getExtension()) !== 'php') {
                    continue;
                }
                $path = $fileInfo->getPathname();
                $relative = str_replace($root . '/', '', $path);
                $contents = file_get_contents($path);
                if ($contents === false || preg_match($namespacePattern, $contents) === 1) {
                    continue;
                }
                $results['total_no_namespace_files']++;
                if (in_array($relative, $allowlist, true)) {
                    $results['intentional_no_namespace_files'][] = $relative;
                } else {
                    $results['unexpected_no_namespace_files'][] = $relative;
                }
            }
        }

        sort($results['intentional_no_namespace_files']);
        sort($results['unexpected_no_namespace_files']);
        $results['intentional_no_namespace_count'] = count($results['intentional_no_namespace_files']);
        $results['unexpected_no_namespace_count'] = count($results['unexpected_no_namespace_files']);

        $outputPath = $root . '/report/rolling-role-w10-no-namespace-audit.json';
        file_put_contents($outputPath, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);
        echo $outputPath . PHP_EOL;
        return 0;
    }
}

namespace {
    exit(\App\Bin\noNamespaceAuditRun());
}
