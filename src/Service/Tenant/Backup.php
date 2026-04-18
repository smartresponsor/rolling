<?php

declare(strict_types=1);

namespace App\Service\Tenant;

use ZipArchive;

/**
 * Backup tenant-specific data:
 * - tuples slice from var/tuples.ndjson where tenant==X
 * - quota/limits configs
 * Output: var/backup/<tenant>_<ts>.zip
 */
final class Backup
{
    /**
     * @param string $varDir
     * @param string $backupDir
     */
    public function __construct(
        private readonly string $varDir = __DIR__ . '/../../../../var',
        private readonly string $backupDir = __DIR__ . '/../../../../var/backup',
    ) {
        if (!is_dir($this->backupDir)) {
            @mkdir($this->backupDir, 0775, true);
        }
    }

    /** @return array{ok:bool, path:string} */
    public function run(string $tenant): array
    {
        $ts = gmdate('Ymd_His');
        $name = sprintf('%s_%s.zip', preg_replace('~[^a-zA-Z0-9_.-]~', '_', $tenant), $ts);
        $zipPath = rtrim($this->backupDir, '/') . '/' . $name;
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return ['ok' => false, 'path' => ''];
        }
        // metadata
        $zip->addFromString('tenant.json', json_encode(['tenant' => $tenant, 'ts' => $ts], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        // tuples slice
        $tuplesPath = rtrim($this->varDir, '/') . '/tuples.ndjson';
        $slice = '';
        if (file_exists($tuplesPath)) {
            $fh = fopen($tuplesPath, 'r');
            if ($fh) {
                while (($line = fgets($fh)) !== false) {
                    $d = json_decode(trim($line), true);
                    if (is_array($d) && ($d['tenant'] ?? null) === $tenant) {
                        $slice .= json_encode($d, JSON_UNESCAPED_SLASHES) . "\n";
                    }
                }
                fclose($fh);
            }
        }
        $zip->addFromString('tuples.ndjson', $slice);
        // quota & limits
        $tenantDir = rtrim($this->varDir, '/') . '/tenants/' . preg_replace('~[^a-zA-Z0-9_.-]~', '_', $tenant);
        foreach (['quota_limits.json', 'quota_usage.json', 'limits.json'] as $f) {
            $p = $tenantDir . '/' . $f;
            if (file_exists($p)) {
                $zip->addFile($p, 'tenants/' . $f);
            }
        }
        $zip->close();
        return ['ok' => true, 'path' => $zipPath];
    }
}
