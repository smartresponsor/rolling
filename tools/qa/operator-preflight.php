<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__, 2);
$commands = [
    'bootstrap_preflight' => ['php', $projectRoot . '/tools/qa/bootstrap-preflight-report.php'],
    'dependency' => ['php', $projectRoot . '/tools/qa/dependency-readiness.php'],
    'recovery' => ['php', $projectRoot . '/tools/qa/recovery-audits.php'],
    'readiness_smoke' => ['php', $projectRoot . '/tools/qa/readiness-smoke.php'],
    'current_summary' => ['php', $projectRoot . '/tools/qa/current-summary.php'],
];

$results = [];

foreach ($commands as $name => $command) {
    $escaped = array_map(static fn (string $part): string => escapeshellarg($part), $command);
    $output = [];
    $exitCode = 0;
    exec(implode(' ', $escaped) . ' 2>&1', $output, $exitCode);

    $results[$name] = [
        'command' => $command,
        'exit_code' => $exitCode,
        'output_preview' => array_slice($output, 0, 20),
    ];
}

$summary = [
    'generated_at_utc' => gmdate('c'),
    'project_root' => $projectRoot,
    'results' => $results,
];

$reportDir = $projectRoot . '/report/recovery';
if (!is_dir($reportDir)) {
    mkdir($reportDir, 0777, true);
}

$reportPath = $reportDir . '/current-operator-preflight.json';
file_put_contents($reportPath, json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);

$pretty = [];
$pretty[] = 'Operator preflight';
$pretty[] = 'Generated at UTC: ' . $summary['generated_at_utc'];
$pretty[] = 'Project root: ' . $projectRoot;
$pretty[] = '';
foreach ($results as $name => $result) {
    $pretty[] = sprintf('[%s] exit=%d', $name, $result['exit_code']);
    foreach ($result['output_preview'] as $line) {
        $pretty[] = '  ' . $line;
    }
    $pretty[] = '';
}
file_put_contents($reportDir . '/current-operator-preflight.pretty.txt', implode(PHP_EOL, $pretty) . PHP_EOL);

echo json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
