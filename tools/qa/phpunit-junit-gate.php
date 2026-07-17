<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$junitArgument = $argv[1] ?? null;

if (!is_string($junitArgument) || '' === trim($junitArgument)) {
    fwrite(STDERR, "Usage: phpunit-junit-gate.php <junit.xml> [--testsuite=<name>] [--filter=<pattern>]\n");
    exit(1);
}

$runPhpunit = count($argv) > 2;
if ($runPhpunit) {
    $temporaryPath = tempnam(sys_get_temp_dir(), 'rolling-phpunit-junit-');
    if (false === $temporaryPath) {
        fail('PHPUnit gate failed: unable to allocate temporary JUnit evidence file.');
    }
    $junitPath = $temporaryPath;
    @unlink($junitPath);
} else {
    $junitPath = absolutePath($root, $junitArgument);
    $junitDir = dirname($junitPath);
    if (is_file($junitDir)) {
        unlink($junitDir);
    }
    if (!is_dir($junitDir)) {
        mkdir($junitDir, 0777, true);
    }
    if (is_dir($junitPath)) {
        fail(sprintf('PHPUnit gate failed: JUnit evidence path is a directory at %s.', relativePath($root, $junitPath)));
    }
}

if ($runPhpunit) {

    $command = [
        PHP_BINARY,
        $root.'/vendor/phpunit/phpunit/phpunit',
        '--configuration='.$root.'/phpunit.xml.dist',
        '--log-junit',
        $junitPath,
        '--no-progress',
    ];

    foreach (array_slice($argv, 2) as $argument) {
        $command[] = $argument;
    }

    passthru(implode(' ', array_map('escapeshellarg', $command)), $phpunitExitCode);
    if (0 !== $phpunitExitCode) {
        fail(sprintf('PHPUnit gate failed: PHPUnit exited with code %d.', $phpunitExitCode));
    }
}

if (!is_file($junitPath)) {
    fail(sprintf('PHPUnit gate failed: JUnit evidence is missing at %s.', relativePath($root, $junitPath)));
}

$junit = (string) file_get_contents($junitPath);
if ('' === trim($junit)) {
    fail('PHPUnit gate failed: JUnit evidence is empty.');
}

libxml_use_internal_errors(true);
$xml = simplexml_load_string($junit);
if (false === $xml) {
    fail('PHPUnit gate failed: JUnit evidence is malformed.');
}

$summary = collectSummary($xml);

if (0 === $summary['tests'] || 0 < $summary['failures'] || 0 < $summary['errors']) {
    fail(sprintf(
        'PHPUnit gate failed: tests=%d failures=%d errors=%d.',
        $summary['tests'],
        $summary['failures'],
        $summary['errors'],
    ));
}

fwrite(STDOUT, sprintf("PHPUnit completion gate passed: %d tests.\n", $summary['tests']));

function absolutePath(string $root, string $path): string
{
    $path = str_replace('\\', '/', $path);
    if (preg_match('/^[A-Za-z]:\//', $path) || str_starts_with($path, '/')) {
        return $path;
    }

    return $root.'/'.ltrim($path, '/');
}

/** @return array{tests:int, failures:int, errors:int} */
function collectSummary(SimpleXMLElement $xml): array
{
    $summary = [
        'tests' => (int) ($xml['tests'] ?? 0),
        'failures' => (int) ($xml['failures'] ?? 0),
        'errors' => (int) ($xml['errors'] ?? 0),
    ];

    if (0 === $summary['tests'] && 'testsuites' === $xml->getName()) {
        foreach ($xml->testsuite as $suite) {
            $summary['tests'] += (int) ($suite['tests'] ?? 0);
            $summary['failures'] += (int) ($suite['failures'] ?? 0);
            $summary['errors'] += (int) ($suite['errors'] ?? 0);
        }
    }

    return $summary;
}

function fail(string $message): never
{
    fwrite(STDERR, $message."\n");
    exit(1);
}

function relativePath(string $root, string $path): string
{
    return str_replace('\\', '/', ltrim(substr($path, strlen($root)), '/\\'));
}
