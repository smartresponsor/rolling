<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Tools\RC;

/**
 *
 */

/**
 *
 */
final class Sha256Sums
{
    /**
     * @param array $files
     * @param string $target
     * @return string
     */
    public static function write(array $files, string $target): string
    {
        $lines = [];
        foreach ($files as $row) {
            $lines[] = $row['sha256'] . "  " . $row['path'];
        }
        $txt = implode("\n", $lines) . "\n";
        @mkdir(dirname($target), 0775, true);
        file_put_contents($target, $txt);
        return $target;
    }
}
