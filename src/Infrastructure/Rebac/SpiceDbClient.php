<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Infrastructure\Rebac;

use App\InfrastructureInterface\Rebac\RebacClientInterface;

/**
 *
 */

/**
 *
 */
class SpiceDbClient implements RebacClientInterface
{
    /**
     * @param \App\Infrastructure\Rebac\HttpClient $http
     */
    public function __construct(private readonly HttpClient $http) {}

    /**
     * @return array
     */
    public function health(): array
    {
        return ['ok' => true, 'backend' => 'spicedb'];
    }

    /**
     * @param string $schemaYaml
     * @return bool
     */
    public function upsertSchema(string $schemaYaml): bool
    {
        // In real life use /v1/schema/write; omitted here.
        return true;
    }

    /**
     * @param array $tuples
     * @return bool
     */
    public function writeTuples(array $tuples): bool
    {
        // For demo, pretend success.
        return true;
    }

    /**
     * @param array $tuples
     * @return bool
     */
    public function deleteTuples(array $tuples): bool
    {
        return true;
    }

    /**
     * @param array $subject
     * @param string $relation
     * @param array $object
     * @param array $context
     * @return bool
     */
    public function check(array $subject, string $relation, array $object, array $context = []): bool
    {
        // In real life: /v1/permissions/check
        return false; // require real backend to answer
    }
}
