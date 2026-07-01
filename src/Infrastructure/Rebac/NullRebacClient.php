<?php

/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Rolling\Infrastructure\Rebac;

use App\Rolling\InfrastructureInterface\Rebac\RebacClientInterface;

class NullRebacClient implements RebacClientInterface
{
    private array $index = [];

    public function health(): array
    {
        return ['ok' => true, 'backend' => 'null'];
    }

    public function upsertSchema(string $schemaYaml): bool
    {
        return true;
    }

    public function writeTuples(array $tuples): bool
    {
        foreach ($tuples as $t) {
            $key = "{$t->userType}:{$t->userId}|{$t->relation}|{$t->objectType}:{$t->objectId}|{$t->tenant}";
            $this->index[$key] = true;
        }

        return true;
    }

    public function deleteTuples(array $tuples): bool
    {
        foreach ($tuples as $t) {
            $key = "{$t->userType}:{$t->userId}|{$t->relation}|{$t->objectType}:{$t->objectId}|{$t->tenant}";
            unset($this->index[$key]);
        }

        return true;
    }

    public function check(array $subject, string $relation, array $object, array $context = []): bool
    {
        $tenant = $context['tenant'] ?? '';
        $key = ($subject['type'] ?? 'user').':'.$subject['id'].'|'.$relation.'|'.($object['type'] ?? 'object').':'.$object['id'].'|'.$tenant;

        return $this->index[$key] ?? false;
    }
}
