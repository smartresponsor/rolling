<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Tools\RC;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

/**
 *
 */

/**
 *
 */
final class Manifest
{
    /** @return array<int,array{path:string,bytes:int,sha256:string}> */
    public static function build(string $root): array
    {
        $rows = [];
        $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS));
        /** @var \SplFileInfo $f */
        foreach ($it as $f) {
            if ($f->isDir()) continue;
            $path = $f->getPathname();
            if (str_contains($path, '/report/')) continue;
            $rel = substr($path, strlen($root) + 1);
            $sha = hash_file('sha256', $path) ?: '';
            $rows[] = ['path' => $rel, 'bytes' => $f->getSize(), 'sha256' => $sha];
        }
        usort($rows, fn($a, $b) => strcmp($a['path'], $b['path']));
        return $rows;
    }
}
