<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\InfrastructureInterface\Policy;

/**
 *
 */

/**
 *
 */
interface GrantRepositoryInterface
{
    /**
     * Return normalized grant rules for given resource type/action within tenant (if any).
     * Each rule example: ['role'=>'admin','action'=>'can_read','resource'=>'order','tenant'=>'t1']
     * or subject-bound: ['user'=>'u1','action'=>'can_write','resource'=>'order']
     * Owner-aware rules may use 'owner' => true to indicate subject.id == resource.ownerId.
     *
     * @param string $resourceType
     * @param string $action
     * @param string|null $tenant
     * @return array<int, array<string, mixed>>
     */
    public function findGrants(string $resourceType, string $action, ?string $tenant): array;

    /**
     * Optional bootstrap (e.g., from NDJSON file).
     */
    public function loadFromNdjson(string $path): void;
}
