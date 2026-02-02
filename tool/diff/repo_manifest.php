#!/usr/bin/env php
<?php
declare(strict_types=1);

/**
 * Produce deterministic manifest (path, bytes, sha256) for all files in repo/…
 */
$root = realpath(__DIR__ . '/../src/') ?: '.';
$iter = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
$rows = [];
foreach ($iter as $f) {
    /** @var SplFileInfo $f */
    if ($f->isDir()) continue;
    $path = $f->getPathname();
    if (str_contains($path, '/report/')) continue;
    $rel = substr($path, strlen($root) + 1);
    $bytes = $f->getSize();
    $sha = hash_file('sha256', $path);
    $rows[] = ['path' => $rel, 'bytes' => $bytes, 'sha256' => $sha];
}
usort($rows, fn($a, $b) => strcmp($a['path'], $b['path']));
@mkdir($root . '/report', 0775, true);
file_put_contents($root . '/report/manifest_g9.json', json_encode(['ts' => date('c'), 'files' => $rows], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo "report/manifest_g9.json written\n";
