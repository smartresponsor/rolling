<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Infrastructure\Residency;

use App\ServiceInterface\Residency\ResidencyPolicyInterface;

/**
 * Writes blobs under var/residency/<region>/<tenant>/<kind>/…
 */
final class ResidencyStorage
{
    /**
     * @param \App\ServiceInterface\Residency\ResidencyPolicyInterface $policy
     * @param string $root
     */
    public function __construct(private readonly ResidencyPolicyInterface $policy, private readonly string $root = __DIR__ . '/../../../var/residency')
    {
        if (!is_dir($this->root)) {
            @mkdir($this->root, 0775, true);
        }
    }

    /**
     * @param string $tenant
     * @param string $kind
     * @param string $name
     * @return string
     */
    public function path(string $tenant, string $kind, string $name): string
    {
        $region = $this->policy->regionForTenant($tenant);
        $dir = $this->root . '/' . $region . '/' . $tenant . '/' . $kind;
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        return $dir . '/' . $name;
    }

    /**
     * @param string $tenant
     * @param string $kind
     * @param string $name
     * @param string $content
     * @return string
     */
    public function write(string $tenant, string $kind, string $name, string $content): string
    {
        $p = $this->path($tenant, $kind, $name);
        file_put_contents($p, $content);
        return $p;
    }

    /**
     * @param string $tenant
     * @param string $kind
     * @param string $name
     * @return string|null
     */
    public function read(string $tenant, string $kind, string $name): ?string
    {
        $p = $this->path($tenant, $kind, $name);
        if (!is_file($p)) {
            return null;
        }
        return (string) file_get_contents($p);
    }
}
