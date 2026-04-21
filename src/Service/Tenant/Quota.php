<?php

declare(strict_types=1);

namespace App\Rolling\Service\Tenant;

/**
 * Per-tenant request quota (fixed 60s window). Stores usage in var/tenants/<tenant>/quota_usage.json
 * and limits in var/tenants/<tenant>/quota_limits.json.
 */
final class Quota
{
    /**
     * @param string $baseDir
     */
    public function __construct(private readonly string $baseDir = __DIR__.'/../../../../var/tenants')
    {
    }

    /**
     * @param string $tenant
     *
     * @return string
     */
    private function dir(string $tenant): string
    {
        return rtrim($this->baseDir, '/').'/'.preg_replace('~[^a-zA-Z0-9_.-]~', '_', $tenant);
    }

    /** @return array{limit_per_min:int} */
    public function getLimit(string $tenant): array
    {
        $path = $this->dir($tenant).'/quota_limits.json';
        if (!is_dir(dirname($path))) {
            @mkdir(dirname($path), 0775, true);
        }
        if (!file_exists($path)) {
            file_put_contents($path, json_encode(['limit_per_min' => 600]));
        }
        $d = json_decode((string) @file_get_contents($path), true);
        $limit = (int) ($d['limit_per_min'] ?? 600);

        return ['limit_per_min' => $limit];
    }

    /**
     * @param string $tenant
     * @param int    $perMin
     *
     * @return void
     */
    public function setLimit(string $tenant, int $perMin): void
    {
        $path = $this->dir($tenant).'/quota_limits.json';
        if (!is_dir(dirname($path))) {
            @mkdir(dirname($path), 0775, true);
        }
        file_put_contents($path, json_encode(['limit_per_min' => max(1, $perMin)], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }

    /** @return array{window_start:int,count:int} */
    private function getUsage(string $tenant): array
    {
        $path = $this->dir($tenant).'/quota_usage.json';
        $now = time();
        $d = json_decode((string) @file_get_contents($path), true);
        $ws = (int) ($d['window_start'] ?? $now);
        $cnt = (int) ($d['count'] ?? 0);
        // reset window if older than 60s
        if ($now - $ws >= 60) {
            $ws = $now;
            $cnt = 0;
        }

        return ['window_start' => $ws, 'count' => $cnt];
    }

    /**
     * @param string $tenant
     * @param array  $u
     *
     * @return void
     */
    private function saveUsage(string $tenant, array $u): void
    {
        $path = $this->dir($tenant).'/quota_usage.json';
        if (!is_dir(dirname($path))) {
            @mkdir(dirname($path), 0775, true);
        }
        file_put_contents($path, json_encode($u));
    }

    /** @return array{allowed:bool, remaining:int, reset:int} */
    public function consume(string $tenant, int $cost = 1): array
    {
        $limit = $this->getLimit($tenant)['limit_per_min'];
        $u = $this->getUsage($tenant);
        $now = time();
        $reset = $u['window_start'] + 60 - $now;
        if ($reset < 0) {
            $reset = 0;
        }
        if ($u['count'] + $cost > $limit) {
            return ['allowed' => false, 'remaining' => max(0, $limit - $u['count']), 'reset' => $reset];
        }
        $u['count'] += $cost;
        $this->saveUsage($tenant, $u);

        return ['allowed' => true, 'remaining' => $limit - $u['count'], 'reset' => $reset];
    }
}
