<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\InfraInterface\Role\Rebac;

use App\Infra\Role\Rebac\Tuple;

/**
 *
 */

/**
 *
 */
interface RebacClientInterface
{
    /**
     * @return array
     */
    public function health(): array;

    /**
     * Upsert namespace/schema definition.
     * @param string $schemaYaml schema or type-system document
     */
    public function upsertSchema(string $schemaYaml): bool;

    /**
     * Write tuples (relations) in batch.
     * @param Tuple[] $tuples
     */
    public function writeTuples(array $tuples): bool;

    /**
     * Delete tuples (relations) in batch.
     * @param Tuple[] $tuples
     */
    public function deleteTuples(array $tuples): bool;

    /**
     * Check permission decision via ReBAC backend.
     * @param array $subject e.g. ['type'=>'user','id'=>'u1']
     * @param string $relation e.g. 'can_read'
     * @param array $object e.g. ['type'=>'order','id'=>'o1']
     * @param array $context optional attributes like tenant
     */
    public function check(array $subject, string $relation, array $object, array $context = []): bool;
}
