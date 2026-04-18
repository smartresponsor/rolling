<?php

declare(strict_types=1);

function role_runtime_requirement_status(string $projectRoot): array
{
    $composerJsonPath = $projectRoot . '/composer.json';
    $composerJson = is_file($composerJsonPath)
        ? json_decode((string) file_get_contents($composerJsonPath), true, 512, JSON_THROW_ON_ERROR)
        : [];

    $requiredPhpConstraint = (string) (($composerJson['require']['php'] ?? ''));
    $requiredExtensions = [];

    foreach (($composerJson['require'] ?? []) as $package => $constraint) {
        if (!is_string($package) || !str_starts_with($package, 'ext-')) {
            continue;
        }

        $requiredExtensions[] = substr($package, 4);
    }

    sort($requiredExtensions);

    $missingExtensions = [];

    foreach ($requiredExtensions as $extension) {
        if (!extension_loaded($extension)) {
            $missingExtensions[] = $extension;
        }
    }

    $vendorAutoloadPath = $projectRoot . '/vendor/autoload.php';
    $composerLockPath = $projectRoot . '/composer.lock';
    $composerBinaryPath = trim((string) shell_exec('command -v composer 2>/dev/null'));

    return [
        'project_root' => $projectRoot,
        'php_version' => PHP_VERSION,
        'required_php_constraint' => $requiredPhpConstraint,
        'required_extensions' => $requiredExtensions,
        'missing_extensions' => $missingExtensions,
        'vendor_autoload_path' => $vendorAutoloadPath,
        'vendor_autoload_present' => is_file($vendorAutoloadPath),
        'composer_lock_present' => is_file($composerLockPath),
        'composer_binary_present' => $composerBinaryPath !== '',
        'composer_binary_path' => $composerBinaryPath !== '' ? $composerBinaryPath : null,
        'ready_for_bootstrap' => is_file($vendorAutoloadPath) && $missingExtensions === [],
    ];
}

function role_runtime_requirement_messages(array $status): array
{
    $lines = [];

    if ($status['vendor_autoload_present'] === false) {
        $lines[] = 'Bootstrap preflight failed: vendor/autoload.php is missing.';
        $lines[] = 'This repository snapshot is source-only and needs Composer dependencies installed before runtime commands can boot.';
        $lines[] = 'Expected path: ' . $status['vendor_autoload_path'];
    }

    if ($status['missing_extensions'] !== []) {
        $lines[] = 'Missing required PHP extensions: ' . implode(', ', $status['missing_extensions']);
    }

    if ($status['composer_binary_present'] === false) {
        $lines[] = 'Composer binary is not available on PATH in the current environment.';
    }

    if ($status['composer_lock_present'] === false) {
        $lines[] = 'composer.lock is missing; dependency graph cannot be reproduced deterministically.';
    }

    if ($lines === []) {
        $lines[] = 'Bootstrap preflight passed.';
        return $lines;
    }

    $lines[] = 'Suggested recovery steps:';
    $lines[] = '  1. Ensure PHP satisfies constraint ' . ($status['required_php_constraint'] ?: '(unspecified)') . '.';

    if ($status['missing_extensions'] !== []) {
        $lines[] = '  2. Install/enable required extensions: ' . implode(', ', $status['missing_extensions']) . '.';
        $lines[] = '  3. Ensure Composer is installed and available on PATH.';
        $lines[] = '  4. Run: composer install';
        $lines[] = '  5. Run: php tools/qa/dependency-readiness.php';
        $lines[] = '  6. Re-run the original command.';
    } else {
        $lines[] = '  2. Ensure Composer is installed and available on PATH.';
        $lines[] = '  3. Run: composer install';
        $lines[] = '  4. Run: php tools/qa/dependency-readiness.php';
        $lines[] = '  5. Re-run the original command.';
    }

    return $lines;
}
