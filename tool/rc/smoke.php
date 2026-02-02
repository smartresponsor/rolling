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
final class Smoke
{
    /** @return array{errors:int, files:int, failures:array<int,array{file:string,error:string}>} */
    public static function run(string $root, array $paths): array
    {
        $errors = 0;
        $files = 0;
        $fails = [];
        foreach ($paths as $p) {
            $dir = $root . '/' . trim($p, '/');
            if (!is_dir($dir)) continue;
            $it = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS));
            /** @var \SplFileInfo $f */
            foreach ($it as $f) {
                if (strtolower($f->getExtension()) !== 'php') continue;
                $files++;
                $cmd = sprintf('php -l %s 2>&1', escapeshellarg($f->getPathname()));
                $out = [];
                $code = 0;
                @exec($cmd, $out, $code);
                if ($code !== 0) {
                    $errors++;
                    $fails[] = ['file' => substr($f->getPathname(), strlen($root) + 1), 'error' => implode("\n", $out)];
                }
            }
        }
        return ['errors' => $errors, 'files' => $files, 'failures' => $fails];
    }
}
