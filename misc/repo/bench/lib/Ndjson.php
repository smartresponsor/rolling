<?php declare(strict_types=1);

namespace App\Bench\Lib;

use Generator;
use RuntimeException;

/**
 *
 */

/**
 *
 */
final class Ndjson
{
    /**
     * @param string $p
     * @return \Generator
     */
    public static function reader(string $p): Generator
    {
        $f = fopen($p, 'r');
        if ($f === false) throw new RuntimeException('open_failed');
        while (!feof($f)) {
            $l = fgets($f);
            if ($l === false) break;
            $l = trim($l);
            if ($l === '') continue;
            $r = json_decode($l, true);
            if (is_array($r)) yield $r;
        }
        fclose($f);
    }

    /**
     * @param $s
     * @param array $row
     * @return void
     */
    /**
     * @param $s
     * @param array $row
     * @return void
     */
    public static function write($s, array $row): void
    {
        fwrite($s, json_encode($row) . "\n");
    }
}
