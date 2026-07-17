<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$stdoutPath = tempnam(sys_get_temp_dir(), 'rolling-phpunit-stdout-');
$stderrPath = tempnam(sys_get_temp_dir(), 'rolling-phpunit-stderr-');

if (false === $stdoutPath || false === $stderrPath) {
    fwrite(STDERR, "Unable to allocate PHPUnit gate output files.\n");
    exit(1);
}

$process = proc_open(
    [
        PHP_BINARY,
        $root.'/vendor/phpunit/phpunit/phpunit',
        '--configuration='.$root.'/phpunit.xml.dist',
        '--testsuite=unit',
        '--testdox',
        '--no-progress',
    ],
    [
        0 => ['pipe', 'r'],
        1 => ['file', $stdoutPath, 'w'],
        2 => ['file', $stderrPath, 'w'],
    ],
    $pipes,
    $root,
);

if (!is_resource($process)) {
    @unlink($stdoutPath);
    @unlink($stderrPath);
    fwrite(STDERR, "Unable to start PHPUnit.\n");
    exit(1);
}

fclose($pipes[0]);
$exitCode = proc_close($process);
$stdout = (string) file_get_contents($stdoutPath);
$stderr = (string) file_get_contents($stderrPath);
@unlink($stdoutPath);
@unlink($stderrPath);

fwrite(STDOUT, $stdout);
fwrite(STDERR, $stderr);

$completed = str_contains($stdout, 'OK (')
    || str_contains($stdout, 'FAILURES!')
    || str_contains($stdout, 'No tests executed!');

if (!$completed) {
    fwrite(STDERR, "PHPUnit gate failed: execution ended without a terminal summary.\n");
    exit(1);
}

if (0 !== $exitCode || str_contains($stdout, 'FAILURES!') || str_contains($stdout, 'No tests executed!')) {
    fwrite(STDERR, sprintf("PHPUnit gate failed with exit code %d.\n", $exitCode));
    exit(1);
}

fwrite(STDOUT, "PHPUnit completion gate passed.\n");
