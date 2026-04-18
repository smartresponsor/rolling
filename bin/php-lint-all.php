#!/usr/bin/env php
<?php

declare(strict_types=1);

$root = dirname(__DIR__);
$iterator = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)
);

$excludeFragments = [
    DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR,
    DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR,
];

$phpFiles = [];
foreach ($iterator as $file) {
    if (!$file->isFile() || $file->getExtension() !== 'php') {
        continue;
    }
    $path = $file->getPathname();
    $skip = false;
    foreach ($excludeFragments as $fragment) {
        if (str_contains($path, $fragment)) {
            $skip = true;
            break;
        }
    }
    if ($skip) {
        continue;
    }
    $phpFiles[] = $path;
}

sort($phpFiles);
$failed = false;
foreach ($phpFiles as $path) {
    $command = escapeshellarg(PHP_BINARY) . ' -l ' . escapeshellarg($path);
    passthru($command, $exitCode);
    if ($exitCode !== 0) {
        $failed = true;
        break;
    }
}

exit($failed ? 1 : 0);
