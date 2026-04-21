<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);

require $root . '/bin/bootstrap-runtime-requirements.php';

$checks = [];
$status = role_runtime_requirement_status($root);

$checks[] = [
    'name' => 'php_version',
    'status' => PHP_VERSION_ID >= 80400 ? 'ok' : 'fail',
    'detail' => PHP_VERSION,
];
$checks[] = [
    'name' => 'dependency_readiness',
    'status' => $status['ready_for_bootstrap'] ? 'ok' : 'warn',
    'detail' => implode("
", role_runtime_requirement_messages($status)),
];

$lintTargets = [
    'bin/bootstrap-preflight.php',
    'bin/bootstrap-runtime-requirements.php',
    'src/Infrastructure/Symfony/RoleBundle.php',
    'src/Infrastructure/Symfony/DependencyInjection/RoleExtension.php',
    'config/services.yaml',
    'tools/qa/dependency-readiness.php',
    'tools/qa/recovery-audits.php',
];

foreach ($lintTargets as $target) {
    $path = $root . '/' . $target;
    if (!is_file($path)) {
        $checks[] = [
            'name' => 'lint:' . $target,
            'status' => 'fail',
            'detail' => 'Missing file',
        ];
        continue;
    }

    $output = [];
    $code = 0;
    exec(sprintf('php -l %s 2>&1', escapeshellarg($path)), $output, $code);
    $checks[] = [
        'name' => 'lint:' . $target,
        'status' => $code === 0 ? 'ok' : 'fail',
        'detail' => implode("
", $output),
    ];
}

$summary = [
    'generated_at' => gmdate('c'),
    'root' => $root,
    'package_mode' => 'symfony_bundle',
    'dependency_status' => $status,
    'checks' => $checks,
];

$reportDir = $root . '/report/recovery';
if (!is_dir($reportDir)) {
    mkdir($reportDir, 0777, true);
}

$json = json_encode($summary, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . PHP_EOL;
file_put_contents($reportDir . '/current-readiness-smoke.json', $json);

echo $json;
