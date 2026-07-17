<?php

declare(strict_types=1);

$paths = ['src', 'tests', 'bin', 'config', 'public'];
$files = [];

foreach ($paths as $path) {
    if (!is_dir($path) && !is_file($path)) {
        continue;
    }

    if (is_file($path)) {
        $files[] = $path;
        continue;
    }

    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path, FilesystemIterator::SKIP_DOTS));
    foreach ($iterator as $file) {
        if ($file->isDir() || $file->getExtension() !== 'php') {
            continue;
        }

        $files[] = $file->getPathname();
    }
}

sort($files);

$workerLimit = max(2, min(8, (int) ($_SERVER['NUMBER_OF_PROCESSORS'] ?? 4)));
$pending = $files;
$running = [];
$failures = [];

while ($pending !== [] || $running !== []) {
    while ($pending !== [] && count($running) < $workerLimit) {
        $file = array_shift($pending);
        $command = sprintf('%s -l %s', escapeshellarg(PHP_BINARY), escapeshellarg($file));
        $pipes = [];
        $process = proc_open($command, [1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);

        if (!is_resource($process)) {
            $failures[$file] = 'Unable to start PHP syntax check.';
            continue;
        }

        stream_set_blocking($pipes[1], false);
        stream_set_blocking($pipes[2], false);
        $running[] = ['file' => $file, 'process' => $process, 'pipes' => $pipes, 'output' => ''];
    }

    foreach ($running as $index => &$job) {
        $job['output'] .= stream_get_contents($job['pipes'][1]);
        $job['output'] .= stream_get_contents($job['pipes'][2]);
        $status = proc_get_status($job['process']);

        if ($status['running']) {
            continue;
        }

        fclose($job['pipes'][1]);
        fclose($job['pipes'][2]);
        $exitCode = proc_close($job['process']);
        if ($exitCode === -1) {
            $exitCode = $status['exitcode'];
        }
        if ($exitCode !== 0) {
            $failures[$job['file']] = trim($job['output']);
        }

        unset($running[$index]);
    }
    unset($job);

    $running = array_values($running);
    if ($running !== []) {
        usleep(10000);
    }
}

if ($failures !== []) {
    foreach ($failures as $file => $message) {
        fwrite(STDERR, sprintf("[%s]\n%s\n", $file, $message));
    }

    exit(1);
}

fwrite(STDOUT, sprintf("Linted %d PHP files with %d parallel workers.\n", count($files), $workerLimit));
