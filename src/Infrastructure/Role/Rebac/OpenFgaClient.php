<?php
/**
 * Copyright (c) 2025 Oleksandr Tishchenko / Marketing America Corp
 * All code comments MUST be in English.
 */
declare(strict_types=1);

namespace App\Infra\Role\Rebac;

use App\InfraInterface\Role\Rebac\RebacClientInterface;

/**
 *
 */

/**
 *
 */
class OpenFgaClient implements RebacClientInterface
{
    /**
     * @param \App\Infra\Role\Rebac\HttpClient $http
     * @param string $storeId
     */
    public function __construct(private readonly HttpClient $http, private readonly string $storeId)
    {
    }

    /**
     * @return array
     */
    public function health(): array
    {
        return ['ok' => true, 'backend' => 'openfga'];
    }

    /**
     * @param string $schemaYaml
     * @return bool
     */
    public function upsertSchema(string $schemaYaml): bool
    {
        // OpenFGA uses type system in JSON; convert is out of scope here.
        // Assume schema already in expected format for demo.
        return true;
    }

    /**
     * @param array $tuples
     * @return bool
     */
    public function writeTuples(array $tuples): bool
    {
        $writes = [];
        foreach ($tuples as $t) {
            $writes[] = [
                'user' => "{$t->userType}:{$t->userId}",
                'relation' => $t->relation,
                'object' => "{$t->objectType}:{$t->objectId}",
            ];
        }
        $res = $this->http->postJson("/stores/{$this->storeId}/write", ['writes' => ['tuple_keys' => $writes]]);
        return $res['ok'] ?? false;
    }

    /**
     * @param array $tuples
     * @return bool
     */
    public function deleteTuples(array $tuples): bool
    {
        $deletes = [];
        foreach ($tuples as $t) {
            $deletes[] = [
                'user' => "{$t->userType}:{$t->userId}",
                'relation' => $t->relation,
                'object' => "{$t->objectType}:{$t->objectId}",
            ];
        }
        $res = $this->http->postJson("/stores/{$this->storeId}/write", ['deletes' => ['tuple_keys' => $deletes]]);
        return $res['ok'] ?? false;
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
        $payload = [
            'tuple_key' => [
                'user' => ($subject['type'] ?? 'user') . ':' . $subject['id'],
                'relation' => $relation,
                'object' => ($object['type'] ?? 'object') . ':' . $object['id'],
            ],
        ];
        $res = $this->http->postJson("/stores/{$this->storeId}/check", $payload);
        if (!($res['ok'] ?? false)) return false;
        return (bool)($res['data']['allowed'] ?? false);
    }
}
