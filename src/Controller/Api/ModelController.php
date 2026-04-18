<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\Model\FileSchemaStorage;
use App\Service\Model\Migrator;
use App\Service\Model\SchemaRegistry;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */

/**
 *
 */
final class ModelController
{
    private SchemaRegistry $registry;
    private Migrator $migrator;

    /**
     * @param string $storagePath
     */
    public function __construct(string $storagePath = __DIR__ . '/../../../../var/role_schema.json')
    {
        $storage = new FileSchemaStorage($storagePath);
        $this->registry = new SchemaRegistry($storage);
        $this->migrator = new Migrator($this->registry);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function versions(): JsonResponse
    {
        return new JsonResponse(['active' => $this->registry->active(), 'versions' => array_keys($this->registry->versions())]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function get(Request $req): JsonResponse
    {
        $v = (string) $req->query->get('version');
        $s = $this->registry->get($v);
        return new JsonResponse(['version' => $v, 'schema' => $s]);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function create(Request $req): JsonResponse
    {
        $payload = json_decode((string) $req->getContent(), true) ?? [];
        $version = $payload['version'] ?? null;
        $schema = $payload['schema'] ?? null;
        if (!$version || !is_array($schema)) {
            return new JsonResponse(['ok' => false, 'error' => 'version/schema required'], 400);
        }
        $res = $this->registry->create($version, $schema);
        return new JsonResponse($res, $res['ok'] ? 200 : 400);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function activate(Request $req): JsonResponse
    {
        $payload = json_decode((string) $req->getContent(), true) ?? [];
        $version = $payload['version'] ?? null;
        if (!$version) {
            return new JsonResponse(['ok' => false, 'error' => 'version required'], 400);
        }
        $ok = $this->registry->activate($version);
        return new JsonResponse(['ok' => $ok, 'active' => $ok ? $version : $this->registry->active()], $ok ? 200 : 404);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function apply(Request $req): JsonResponse
    {
        $payload = json_decode((string) $req->getContent(), true) ?? [];
        $version = $payload['version'] ?? null;
        $schema = $payload['schema'] ?? null;
        $dry = (bool) ($payload['dry_run'] ?? false);
        if (!$version || !is_array($schema)) {
            return new JsonResponse(['ok' => false, 'error' => 'version/schema required'], 400);
        }
        $res = $this->migrator->apply($version, $schema, $dry);
        return new JsonResponse($res, $res['ok'] ? 200 : 400);
    }
}
