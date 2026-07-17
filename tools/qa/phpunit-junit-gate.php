<?php

declare(strict_types=1);

$reportPath = $argv[1] ?? '';

if ('' === $reportPath || !is_file($reportPath)) {
    fwrite(STDERR, "PHPUnit JUnit report is missing.\n");
    exit(1);
}

$contents = file_get_contents($reportPath);

if (false === $contents || !preg_match('/<testsuites\\b([^>]*)>/', $contents, $rootMatch)) {
    fwrite(STDERR, "PHPUnit JUnit report is incomplete or invalid.\n");
    exit(1);
}

$attributes = [];
preg_match_all('/\\b(tests|errors|failures|skipped)="(\\d+)"/', $rootMatch[1], $matches, PREG_SET_ORDER);

foreach ($matches as $match) {
    $attributes[$match[1]] = (int) $match[2];
}

$tests = $attributes['tests'] ?? 0;
$errors = $attributes['errors'] ?? -1;
$failures = $attributes['failures'] ?? -1;
$skipped = $attributes['skipped'] ?? 0;

if ($tests < 1 || 0 !== $errors || 0 !== $failures) {
    fwrite(
        STDERR,
        sprintf(
            "PHPUnit JUnit gate failed: tests=%d errors=%d failures=%d skipped=%d.\n",
            $tests,
            $errors,
            $failures,
            $skipped,
        ),
    );
    exit(1);
}

fwrite(
    STDOUT,
    sprintf(
        "PHPUnit JUnit gate passed: tests=%d errors=0 failures=0 skipped=%d.\n",
        $tests,
        $skipped,
    ),
);
