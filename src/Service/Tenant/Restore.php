<?php

declare(strict_types=1);

namespace App\Rolling\Service\Tenant;

/**
 * Restore tenant data from a zip produced by Backup.
 * - Appends tuples.ndjson slice
 * - Restores quotas/limits files.
 */
final class Restore
{
    /**
     * @param string $varDir
     */
    public function __construct(private readonly string $varDir = __DIR__.'/../../../../var')
    {
    }

    /** @return array{ok:bool, tuples:int} */
    public function run(string $zipPath): array
    {
        $zip = new \ZipArchive();
        if (true !== $zip->open($zipPath)) {
            return ['ok' => false, 'tuples' => 0];
        }
        // tuples
        $tuplesCnt = 0;
        $slice = $zip->getFromName('tuples.ndjson');
        if (is_string($slice) && '' !== $slice) {
            $path = rtrim($this->varDir, '/').'/tuples.ndjson';
            file_put_contents($path, $slice, FILE_APPEND);
            $tuplesCnt = substr_count($slice, "\n");
        }
        // tenants files
        $tenantDir = rtrim($this->varDir, '/').'/tenants';
        for ($i = 0; $i < $zip->numFiles; ++$i) {
            $stat = $zip->statIndex($i);
            $name = $stat['name'] ?? '';
            if (str_starts_with($name, 'tenants/')) {
                $content = $zip->getFromIndex($i);
                $out = $tenantDir.'/'.basename($name);
                if (!is_dir(dirname($out))) {
                    @mkdir(dirname($out), 0775, true);
                }
                file_put_contents($out, $content);
            }
        }
        $zip->close();

        return ['ok' => true, 'tuples' => $tuplesCnt];
    }
}
