<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__, 2);

require $projectRoot . '/bin/bootstrap-runtime-requirements.php';

$status = role_runtime_requirement_status($projectRoot);
$messages = role_runtime_requirement_messages($status);

$summary = [
    'generated_at_utc' => gmdate('c'),
    'project_root' => $projectRoot,
    'ready_for_bootstrap' => $status['ready_for_bootstrap'],
    'messages' => $messages,
];

$reportDir = $projectRoot . '/report/recovery';
if (!is_dir($reportDir)) {
    mkdir($reportDir, 0777, true);
}

file_put_contents(
    $reportDir . '/current-bootstrap-preflight.json',
    json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL
);

$pretty = [];
$pretty[] = 'Bootstrap preflight';
$pretty[] = 'Generated at UTC: ' . $summary['generated_at_utc'];
$pretty[] = 'Project root: ' . $projectRoot;
$pretty[] = 'Ready for bootstrap: ' . ($summary['ready_for_bootstrap'] ? 'yes' : 'no');
$pretty[] = '';
foreach ($messages as $message) {
    $pretty[] = $message;
}
file_put_contents($reportDir . '/current-bootstrap-preflight.pretty.txt', implode(PHP_EOL, $pretty) . PHP_EOL);

echo json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;

exit($summary['ready_for_bootstrap'] ? 0 : 1);
