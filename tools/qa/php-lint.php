<?php

declare(strict_types=1);

$paths = ['src', 'Http', 'Policy', 'PolicyInterface', 'Service', 'tests', 'bin', 'public'];
$files = [];

foreach ($paths as $path) {
    if (!is_dir($path) && !is_file($path)) {
        continue;
    }

    if (is_file($path)) {
        $files[] = $path;
        continue;
    }

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));
    foreach ($iterator as $file) {
        if ($file->isDir() || $file->getExtension() !== 'php') {
            continue;
        }

        $files[] = $file->getPathname();
    }
}

sort($files);

$failures = [];

foreach ($files as $file) {
    $command = sprintf('php -l %s 2>&1', escapeshellarg($file));
    exec($command, $output, $exitCode);
    if ($exitCode !== 0) {
        $failures[$file] = implode(PHP_EOL, $output);
    }
    $output = [];
}

if ($failures !== []) {
    foreach ($failures as $file => $message) {
        fwrite(STDERR, sprintf("[%s]\n%s\n", $file, $message));
    }

    exit(1);
}

fwrite(STDOUT, sprintf("Linted %d PHP files.\n", count($files)));
