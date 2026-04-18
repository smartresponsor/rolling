<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__, 2);
$reportDir = $projectRoot . '/report/recovery';
if (!is_dir($reportDir)) {
    mkdir($reportDir, 0777, true);
}

$readJson = static function (string $path): array {
    if (!is_file($path)) {
        return ['available' => false];
    }

    $decoded = json_decode((string) file_get_contents($path), true);

    return is_array($decoded)
        ? ['available' => true, 'data' => $decoded]
        : ['available' => true, 'invalid_json' => true];
};

$summary = [
    'generated_at' => gmdate(DATE_ATOM),
    'artifacts' => [
        'bootstrap_preflight' => $readJson($reportDir . '/current-bootstrap-preflight.json'),
        'dependency_readiness' => $readJson($reportDir . '/current-dependency-readiness.json'),
        'readiness_smoke' => $readJson($reportDir . '/current-readiness-smoke.json'),
        'operator_preflight' => $readJson($reportDir . '/current-operator-preflight.json'),
        'recovery_audits' => $readJson($reportDir . '/current-recovery-audits.json'),
        'autoload_audit' => $readJson($reportDir . '/current-autoload-audit.json'),
        'canon_scan' => $readJson($reportDir . '/current-canon-scan.json'),
        'namespace_audit' => $readJson($reportDir . '/current-namespace-audit.json'),
    ],
];

$readyForBootstrap = $summary['artifacts']['dependency_readiness']['data']['ready_for_bootstrap'] ?? null;
$autoloadBroken = $summary['artifacts']['autoload_audit']['data']['broken_entries'] ?? null;
$externalRoots = $summary['artifacts']['canon_scan']['data']['external_root_count'] ?? null;
$nonAppDrift = $summary['artifacts']['namespace_audit']['data']['non_app_drift_in_active_roots'] ?? null;
$missingExtensions = $summary['artifacts']['dependency_readiness']['data']['missing_extensions'] ?? [];
$composerOnPath = $summary['artifacts']['dependency_readiness']['data']['composer_on_path'] ?? null;
$vendorAutoloadExists = $summary['artifacts']['dependency_readiness']['data']['vendor_autoload_exists'] ?? null;

$blockers = [];
if ($readyForBootstrap === false) {
    $blockers[] = 'Bootstrap is not ready.';
}
if ($vendorAutoloadExists === false) {
    $blockers[] = 'vendor/autoload.php is missing.';
}
if ($composerOnPath === false) {
    $blockers[] = 'Composer is not available on PATH.';
}
if (is_array($missingExtensions) && $missingExtensions !== []) {
    $blockers[] = 'Missing PHP extensions: ' . implode(', ', $missingExtensions) . '.';
}
if (is_int($autoloadBroken) && $autoloadBroken > 0) {
    $blockers[] = 'Autoload continuity audit reports broken entries.';
}
if (is_int($externalRoots) && $externalRoots > 0) {
    $blockers[] = 'Canon scan reports external root drift.';
}
if (is_int($nonAppDrift) && $nonAppDrift > 0) {
    $blockers[] = 'Namespace audit reports non-App drift in active roots.';
}

$summary['status'] = [
    'ready_for_bootstrap' => $readyForBootstrap,
    'autoload_broken_entries' => $autoloadBroken,
    'external_root_count' => $externalRoots,
    'non_app_drift_in_active_roots' => $nonAppDrift,
    'composer_on_path' => $composerOnPath,
    'vendor_autoload_exists' => $vendorAutoloadExists,
    'missing_extensions' => $missingExtensions,
    'blockers' => $blockers,
];

file_put_contents(
    $reportDir . '/current-summary.json',
    (string) json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
);

$pretty = [];
$pretty[] = 'Current recovery summary';
$pretty[] = 'Generated at UTC: ' . $summary['generated_at'];
$pretty[] = '';
$pretty[] = 'Status';
$pretty[] = '  Ready for bootstrap: ' . ($readyForBootstrap === true ? 'yes' : ($readyForBootstrap === false ? 'no' : 'unknown'));
$pretty[] = '  Composer on PATH: ' . ($composerOnPath === true ? 'yes' : ($composerOnPath === false ? 'no' : 'unknown'));
$pretty[] = '  vendor/autoload.php exists: ' . ($vendorAutoloadExists === true ? 'yes' : ($vendorAutoloadExists === false ? 'no' : 'unknown'));
$pretty[] = '  Missing extensions: ' . (is_array($missingExtensions) && $missingExtensions !== [] ? implode(', ', $missingExtensions) : '(none)');
$pretty[] = '  Autoload broken entries: ' . (is_int($autoloadBroken) ? (string) $autoloadBroken : 'unknown');
$pretty[] = '  External root count: ' . (is_int($externalRoots) ? (string) $externalRoots : 'unknown');
$pretty[] = '  Non-App drift in active roots: ' . (is_int($nonAppDrift) ? (string) $nonAppDrift : 'unknown');
$pretty[] = '';
$pretty[] = 'Blockers';
if ($blockers === []) {
    $pretty[] = '  (none)';
} else {
    foreach ($blockers as $blocker) {
        $pretty[] = '  - ' . $blocker;
    }
}
$pretty[] = '';
$pretty[] = 'Artifacts';
foreach ($summary['artifacts'] as $name => $artifact) {
    $pretty[] = sprintf('  %s: %s', $name, ($artifact['available'] ?? false) ? 'available' : 'missing');
}

file_put_contents($reportDir . '/current-summary.pretty.txt', implode(PHP_EOL, $pretty) . PHP_EOL);

echo $reportDir . '/current-summary.json' . PHP_EOL;
