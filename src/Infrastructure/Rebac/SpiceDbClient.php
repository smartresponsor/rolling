<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Infrastructure\Rebac;

use App\Rolling\InfrastructureInterface\Rebac\RebacClientInterface;

class SpiceDbClient implements RebacClientInterface
{
    public function __construct(private readonly RebacRelationshipHttpJsonClient $http)
    {
    }

    public function health(): array
    {
        return ['ok' => true, 'backend' => 'spicedb'];
    }

    public function upsertSchema(string $schemaYaml): bool
    {
        // In real life use /v1/schema/write; omitted here.
        return true;
    }

    public function writeTuples(array $tuples): bool
    {
        // For demo, pretend success.
        return true;
    }

    public function deleteTuples(array $tuples): bool
    {
        return true;
    }

    public function check(array $subject, string $relation, array $object, array $context = []): bool
    {
        // In real life: /v1/permissions/check
        return false; // require real backend to answer
    }
}
