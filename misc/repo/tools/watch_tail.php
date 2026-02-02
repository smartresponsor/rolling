#!/usr/bin/env php
<?php
declare(strict_types=1);
/**
 * Tail-like reader printing new NDJSON lines with offsets for /v2/tuples/watch simulation.
 */
$path = $argv[1] ?? __DIR__ . '/../var/tuples.ndjson';
$offset = intval($argv[2] ?? 0);
$f = fopen($path, 'c+');
if (!$f) {
    fwrite(STDERR, "cannot open $path\n");
    exit(2);
}
fseek($f, $offset);
while (true) {
    $line = fgets($f);
    if ($line === false) {
        usleep(200000);
        clearstatcache();
        continue;
    }
    $offset = ftell($f);
    echo json_encode(['offset' => $offset, 'data' => trim($line)], JSON_UNESCAPED_SLASHES), PHP_EOL;
}
