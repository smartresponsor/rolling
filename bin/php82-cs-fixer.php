#!/usr/bin/env php
<?php

declare(strict_types=1);

$php82 = getenv('PHP82_BIN');
if (!is_string($php82) || $php82 === '') {
    fwrite(STDERR, "Set PHP82_BIN to the PHP 8.2 executable path before running php-cs-fixer.\n");
    exit(2);
}

$mode = $_SERVER['argv'][1] ?? 'check';
$arguments = match ($mode) {
    'fix' => ['fix', '--config=.php-cs-fixer.dist.php'],
    default => ['fix', '--dry-run', '--diff', '--config=.php-cs-fixer.dist.php'],
};

$bin = dirname(__DIR__) . '/vendor/bin/php-cs-fixer';
if (!file_exists($bin)) {
    fwrite(STDERR, "php-cs-fixer is not installed. Run composer install first.\n");
    exit(3);
}

$command = array_merge([$php82, $bin], $arguments);
$parts = array_map(static fn (string $part): string => escapeshellarg($part), $command);
passthru(implode(' ', $parts), $exitCode);
exit($exitCode);
