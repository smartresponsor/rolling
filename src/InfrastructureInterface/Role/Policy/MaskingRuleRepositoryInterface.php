<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\InfraInterface\Role\Policy;

/**
 *
 */

/**
 *
 */
interface MaskingRuleRepositoryInterface
{
    /**
     * Return list of masking rules (normalized arrays) for resource/action/tenant/role conditions.
     * Rule example:
     * [
     *   'id' => 'r1',
     *   'resource' => 'user',
     *   'action' => 'can_read',
     *   'tenant' => 't1|null',
     *   'role' => 'support|null',
     *   'mask' => [
     *      'drop'   => ['ssn'],
     *      'redact' => ['email'],
     *      'hash'   => ['fullName']
     *   ]
     * ]
     *
     * @return array<int, array<string, mixed>>
     */
    public function find(string $resourceType, string $action, ?string $tenant, array $roles): array;

    /**
     * @param string $path
     * @return void
     */
    public function loadFromNdjson(string $path): void;
}
