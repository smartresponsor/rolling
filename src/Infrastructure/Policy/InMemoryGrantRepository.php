<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Infrastructure\Policy;

use App\InfrastructureInterface\Policy\GrantRepositoryInterface;

/**
 *
 */

/**
 *
 */
final class InMemoryGrantRepository implements GrantRepositoryInterface
{
    /** @var array */
    private array $grants;

    /**
     * @param array $seed
     */
    public function __construct(array $seed = [])
    {
        $this->grants = $seed;
    }

    /**
     * @param string $path
     * @return void
     */
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

    /**
     * @param string $resourceType
     * @param string $action
     * @param string|null $tenant
     * @return array
     */
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
            if (isset($g['tenant']) && $tenant !== null && $g['tenant'] !== $tenant) {
                continue;
            }
            $out[] = $g;
        }
        return $out;
    }
}
