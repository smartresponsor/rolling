<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$stdoutPath = tempnam(sys_get_temp_dir(), 'rolling-phpunit-stdout-');
$stderrPath = tempnam(sys_get_temp_dir(), 'rolling-phpunit-stderr-');
$junitPath = tempnam(sys_get_temp_dir(), 'rolling-phpunit-junit-');

if (false === $stdoutPath || false === $stderrPath || false === $junitPath) {
    fwrite(STDERR, "Unable to allocate PHPUnit gate output files.\n");
    exit(1);
}

// PHPUnit owns creation of the JUnit report and must receive a non-existent target path.
@unlink($junitPath);

$process = proc_open(
    [
        PHP_BINARY,
        $root.'/vendor/phpunit/phpunit/phpunit',
        '--configuration='.$root.'/phpunit.xml.dist',
        '--testsuite=unit',
        '--log-junit',
        $junitPath,
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
$junit = (string) file_get_contents($junitPath);
@unlink($stdoutPath);
@unlink($stderrPath);
@unlink($junitPath);

fwrite(STDOUT, $stdout);
fwrite(STDERR, $stderr);

if ('' === trim($junit)) {
    fwrite(STDERR, sprintf(
        "PHPUnit gate failed: JUnit evidence was not produced (exit=%d, stdout_bytes=%d, stderr_bytes=%d).\n",
        $exitCode,
        strlen($stdout),
        strlen($stderr),
    ));
    exit(1);
}

libxml_use_internal_errors(true);
$xml = simplexml_load_string($junit);
if (false === $xml) {
    fwrite(STDERR, "PHPUnit gate failed: JUnit evidence is malformed.\n");
    exit(1);
}

$tests = (int) ($xml['tests'] ?? 0);
$failures = (int) ($xml['failures'] ?? 0);
$errors = (int) ($xml['errors'] ?? 0);

if (0 === $tests && 'testsuites' === $xml->getName()) {
    foreach ($xml->testsuite as $suite) {
        $tests += (int) ($suite['tests'] ?? 0);
        $failures += (int) ($suite['failures'] ?? 0);
        $errors += (int) ($suite['errors'] ?? 0);
    }
}

if (0 !== $exitCode || 0 === $tests || 0 < $failures || 0 < $errors) {
    fwrite(STDERR, sprintf(
        "PHPUnit gate failed: exit=%d tests=%d failures=%d errors=%d.\n",
        $exitCode,
        $tests,
        $failures,
        $errors,
    ));
    exit(1);
}

fwrite(STDOUT, sprintf("PHPUnit completion gate passed: %d tests.\n", $tests));
