<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Infra\Role\Policy\Masking;

use App\InfraInterface\Role\Policy\MaskingRuleRepositoryInterface;

/**
 *
 */

/**
 *
 */
final class InMemoryMaskingRuleRepository implements MaskingRuleRepositoryInterface
{
    /** @var array */
    private array $rules;

    /**
     * @param array $seed
     */
    public function __construct(array $seed = [])
    {
        $this->rules = $seed;
    }

    /**
     * @param string $path
     * @return void
     */
    public function loadFromNdjson(string $path): void
    {
        if (!is_file($path)) return;
        $fh = fopen($path, 'r');
        while (($line = fgets($fh)) !== false) {
            $row = json_decode($line, true);
            if (is_array($row)) $this->rules[] = $row;
        }
        fclose($fh);
    }

    /**
     * @param string $resourceType
     * @param string $action
     * @param string|null $tenant
     * @param array $roles
     * @return array
     */
    public function find(string $resourceType, string $action, ?string $tenant, array $roles): array
    {
        $out = [];
        foreach ($this->rules as $r) {
            if (($r['resource'] ?? null) !== $resourceType) continue;
            if (($r['action'] ?? null) !== $action) continue;
            if (isset($r['tenant']) && $tenant !== null && $r['tenant'] !== $tenant) continue;
            $roleCond = $r['role'] ?? null;
            if ($roleCond !== null && !in_array($roleCond, $roles, true)) continue;
            $out[] = $r;
        }
        return $out;
    }
}
