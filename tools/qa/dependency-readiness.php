<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__, 2);

require $projectRoot . '/bin/bootstrap-runtime-requirements.php';

$status = role_runtime_requirement_status($projectRoot);
$summary = [
    'checked_at_utc' => gmdate('c'),
    'project_root' => $status['project_root'],
    'php_version' => $status['php_version'],
    'required_php_constraint' => $status['required_php_constraint'],
    'required_extensions' => $status['required_extensions'],
    'missing_extensions' => $status['missing_extensions'],
    'vendor_autoload_present' => $status['vendor_autoload_present'],
    'composer_lock_present' => $status['composer_lock_present'],
    'composer_binary_present' => $status['composer_binary_present'],
    'composer_binary_path' => $status['composer_binary_path'],
    'ready_for_bootstrap' => $status['ready_for_bootstrap'],
    'messages' => role_runtime_requirement_messages($status),
];

$reportDir = $projectRoot . '/report/recovery';
if (!is_dir($reportDir)) {
    mkdir($reportDir, 0777, true);
}

$json = json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
file_put_contents($reportDir . '/current-dependency-readiness.json', $json);

$pretty = [];
$pretty[] = 'Dependency readiness';
$pretty[] = 'Checked at UTC: ' . $summary['checked_at_utc'];
$pretty[] = 'Project root: ' . $summary['project_root'];
$pretty[] = 'PHP version: ' . $summary['php_version'];
$pretty[] = 'Required PHP constraint: ' . ($summary['required_php_constraint'] ?: '(unspecified)');
$pretty[] = 'Composer binary present: ' . ($summary['composer_binary_present'] ? 'yes' : 'no');
$pretty[] = 'Composer binary path: ' . ($summary['composer_binary_path'] ?? '(not found)');
$pretty[] = 'composer.lock present: ' . ($summary['composer_lock_present'] ? 'yes' : 'no');
$pretty[] = 'vendor/autoload.php present: ' . ($summary['vendor_autoload_present'] ? 'yes' : 'no');
$pretty[] = 'Required extensions: ' . ($summary['required_extensions'] !== [] ? implode(', ', $summary['required_extensions']) : '(none)');
$pretty[] = 'Missing extensions: ' . ($summary['missing_extensions'] !== [] ? implode(', ', $summary['missing_extensions']) : '(none)');
$pretty[] = 'Ready for bootstrap: ' . ($summary['ready_for_bootstrap'] ? 'yes' : 'no');
$pretty[] = '';
$pretty[] = 'Messages:';
foreach ($summary['messages'] as $line) {
    $pretty[] = '- ' . $line;
}
file_put_contents($reportDir . '/current-dependency-readiness.pretty.txt', implode(PHP_EOL, $pretty) . PHP_EOL);

fwrite(STDOUT, $json);
