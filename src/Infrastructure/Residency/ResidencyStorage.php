<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Infrastructure\Residency;

use App\Rolling\ServiceInterface\Residency\ResidencyPolicyInterface;

/**
 * Writes blobs under var/residency/<region>/<tenant>/<kind>/….
 */
final class ResidencyStorage
{
    public function __construct(private readonly ResidencyPolicyInterface $policy, private readonly string $root = __DIR__.'/../../../var/residency')
    {
        if (!is_dir($this->root)) {
            @mkdir($this->root, 0775, true);
        }
    }

    public function path(string $tenant, string $kind, string $nameEntity): string
    {
        $region = $this->policy->regionForTenant($tenant);
        $dir = $this->root.'/'.$region.'/'.$tenant.'/'.$kind;
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        return $dir.'/'.$nameEntity;
    }

    public function write(string $tenant, string $kind, string $nameEntity, string $content): string
    {
        $p = $this->path($tenant, $kind, $nameEntity);
        file_put_contents($p, $content);

        return $p;
    }

    public function read(string $tenant, string $kind, string $nameEntity): ?string
    {
        $p = $this->path($tenant, $kind, $nameEntity);
        if (!is_file($p)) {
            return null;
        }

        return (string) file_get_contents($p);
    }
}
