<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Infrastructure\Policy;

use App\Rolling\InfrastructureInterface\Policy\GrantRepositoryInterface;

final class InMemoryGrantRepository implements GrantRepositoryInterface
{
    private array $grants;

    public function __construct(array $seed = [])
    {
        $this->grants = $seed;
    }

    public function loadFromNdjson(string $path): void
    {
        if (!is_file($path)) {
            return;
        }
        $fh = fopen($path, 'r');
        while (($line = fgets($fh)) !== false) {
            $row = json_decode($line, true);
            if (is_array($row)) {
                $this->grants[] = $row;
            }
        }
        fclose($fh);
    }

    public function findGrants(string $resourceType, string $action, ?string $tenant): array
    {
        $out = [];
        foreach ($this->grants as $g) {
            if (($g['resource'] ?? null) !== $resourceType) {
                continue;
            }
            if (($g['action'] ?? null) !== $action) {
                continue;
            }
            if (isset($g['tenant']) && null !== $tenant && $g['tenant'] !== $tenant) {
                continue;
            }
            $out[] = $g;
        }

        return $out;
    }
}
