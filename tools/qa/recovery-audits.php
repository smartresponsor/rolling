<?php

declare(strict_types=1);

$projectRoot = dirname(__DIR__, 2);
$commands = [
    'autoload' => ['php', $projectRoot . '/bin/autoload-continuity-audit.php'],
    'canon' => ['php', $projectRoot . '/bin/canon-scan.php'],
    'namespace' => ['php', $projectRoot . '/bin/namespace-audit.php'],
    'dependency' => ['php', $projectRoot . '/tools/qa/dependency-readiness.php'],
];

$results = [
    'generated_at_utc' => gmdate('c'),
    'project_root' => $projectRoot,
    'commands' => [],
];

foreach ($commands as $name => $command) {
    $escaped = implode(' ', array_map('escapeshellarg', $command));
    $output = [];
    $exitCode = 0;
    exec($escaped . ' 2>&1', $output, $exitCode);

    $results['commands'][$name] = [
        'command' => $command,
        'exit_code' => $exitCode,
        'output' => $output,
    ];
}

$results['ready_for_operator_review'] =
    ($results['commands']['autoload']['exit_code'] ?? 1) === 0 &&
    ($results['commands']['canon']['exit_code'] ?? 1) === 0 &&
    ($results['commands']['namespace']['exit_code'] ?? 1) === 0 &&
    ($results['commands']['dependency']['exit_code'] ?? 1) === 0;

$reportDir = $projectRoot . '/report/recovery';
$reportPath = $reportDir . '/current-recovery-audits.json';
file_put_contents($reportPath, json_encode($results, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL);

$pretty = [];
$pretty[] = 'Recovery audits';
$pretty[] = 'Generated at UTC: ' . $results['generated_at_utc'];
$pretty[] = 'Project root: ' . $projectRoot;
$pretty[] = 'Ready for operator review: ' . ($results['ready_for_operator_review'] ? 'yes' : 'no');
$pretty[] = '';
foreach ($results['commands'] as $name => $result) {
    $pretty[] = sprintf('[%s] exit=%d', $name, $result['exit_code']);
    foreach (array_slice($result['output'], 0, 20) as $line) {
        $pretty[] = '  ' . $line;
    }
    $pretty[] = '';
}
file_put_contents($reportDir . '/current-recovery-audits.pretty.txt', implode(PHP_EOL, $pretty) . PHP_EOL);

fwrite(STDOUT, $reportPath . PHP_EOL);
