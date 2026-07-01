<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Infrastructure\Policy\Masking;

use App\Rolling\InfrastructureInterface\Policy\MaskingRuleRepositoryInterface;

final class InMemoryMaskingRuleRepository implements MaskingRuleRepositoryInterface
{
    private array $rules;

    public function __construct(array $seed = [])
    {
        $this->rules = $seed;
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
                $this->rules[] = $row;
            }
        }
        fclose($fh);
    }

    public function find(string $resourceType, string $action, ?string $tenant, array $roles): array
    {
        $out = [];
        foreach ($this->rules as $r) {
            if (($r['resource'] ?? null) !== $resourceType) {
                continue;
            }
            if (($r['action'] ?? null) !== $action) {
                continue;
            }
            if (isset($r['tenant']) && null !== $tenant && $r['tenant'] !== $tenant) {
                continue;
            }
            $roleCond = $r['role'] ?? null;
            if (null !== $roleCond && !in_array($roleCond, $roles, true)) {
                continue;
            }
            $out[] = $r;
        }

        return $out;
    }
}
