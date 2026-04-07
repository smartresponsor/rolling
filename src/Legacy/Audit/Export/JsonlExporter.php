<?php
declare(strict_types=1);

namespace App\Legacy\Audit\Export;

use RuntimeException;

/**
 *
 */

/**
 *
 */
final class JsonlExporter implements ExporterInterface
{
    /**
     * @param iterable $records
     * @param string $path
     * @return void
     */
    public function export(iterable $records, string $path): void
    {
        $f = fopen($path, 'wb');
        if ($f === false) throw new RuntimeException('Cannot open file for write: ' . $path);
        foreach ($records as $r) {
            fwrite($f, json_encode($r->toArray(), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . "\n");
        }
        fclose($f);
    }
}
