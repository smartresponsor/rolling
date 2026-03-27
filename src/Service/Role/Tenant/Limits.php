<?php

declare(strict_types=1);

namespace Tenant;

/**
 * Static per-tenant limits (e.g., max tuples, residency region). Stored in var/tenants/<tenant>/limits.json
 */
final class Limits
{
    /**
     * @param string $baseDir
     */
    public function __construct(private readonly string $baseDir = __DIR__ . '/../../../../var/tenants') {}

    /**
     * @param string $tenant
     * @return string
     */
    private function path(string $tenant): string
    {
        $dir = rtrim($this->baseDir, '/') . '/' . preg_replace('~[^a-zA-Z0-9_.-]~', '_', $tenant);
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        return $dir . '/limits.json';
    }

    /** @return array{max_tuples:int|null,residency:string|null} */
    public function get(string $tenant): array
    {
        $p = $this->path($tenant);
        $d = json_decode((string) @file_get_contents($p), true);
        return [
            'max_tuples' => isset($d['max_tuples']) ? (int) $d['max_tuples'] : null,
            'residency' => isset($d['residency']) ? (string) $d['residency'] : null,
        ];
    }

    /**
     * @param string $tenant
     * @param int|null $maxTuples
     * @param string|null $residency
     * @return void
     */
    public function set(string $tenant, ?int $maxTuples, ?string $residency): void
    {
        $p = $this->path($tenant);
        file_put_contents($p, json_encode(['max_tuples' => $maxTuples, 'residency' => $residency], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
