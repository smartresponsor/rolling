<?php

declare(strict_types=1);

namespace App\Rolling\Controller\Api;

use App\Rolling\Infrastructure\Security\HmacKeyFsStore;
use App\Rolling\Infrastructure\Security\JwksFsVerifier;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class SecurityController
{
    /**
     * @param string $baseDir
     */
    public function __construct(private readonly string $baseDir = __DIR__.'/../../../../var')
    {
    }

    /**
     * @return JwksFsVerifier
     */
    private function svc(): JwksFsVerifier
    {
        return new JwksFsVerifier(new HmacKeyFsStore($this->baseDir.'/keys'));
    }

    /**
     * @param Request $req
     *
     * @return JsonResponse
     */
    public function sign(Request $req): JsonResponse
    {
        $p = json_decode((string) $req->getContent(), true) ?? [];
        $tenant = (string) ($p['tenant'] ?? 't1');
        $payload = (array) ($p['claims'] ?? []);
        $jwt = $this->svc()->signHs256($tenant, $payload);

        return new JsonResponse(['jwt' => $jwt], 200);
    }

    /**
     * @param Request $req
     *
     * @return JsonResponse
     */
    public function verify(Request $req): JsonResponse
    {
        $p = json_decode((string) $req->getContent(), true) ?? [];
        $tenant = (string) ($p['tenant'] ?? 't1');
        $jwt = (string) ($p['token'] ?? '');
        $res = $this->svc()->verify($tenant, $jwt);

        return new JsonResponse($res, 200);
    }

    /**
     * @param Request $req
     *
     * @return JsonResponse
     */
    public function rotate(Request $req): JsonResponse
    {
        $p = json_decode((string) $req->getContent(), true) ?? [];
        $tenant = (string) ($p['tenant'] ?? 't1');
        $note = isset($p['note']) ? (string) $p['note'] : null;
        $store = new HmacKeyFsStore($this->baseDir.'/keys');
        $kid = $store->rotateHmac($tenant, $note);

        return new JsonResponse(['kid' => $kid], 200);
    }

    /**
     * @param Request $req
     *
     * @return JsonResponse
     */
    public function jwksGet(Request $req): JsonResponse
    {
        $tenant = (string) ($req->query->get('tenant') ?? 't1');
        $store = new HmacKeyFsStore($this->baseDir.'/keys');

        return new JsonResponse($store->jwks($tenant), 200);
    }

    /**
     * @param Request $req
     *
     * @return JsonResponse
     */
    public function jwksPut(Request $req): JsonResponse
    {
        $p = json_decode((string) $req->getContent(), true) ?? [];
        $tenant = (string) ($p['tenant'] ?? 't1');
        $jwks = (array) ($p['jwks'] ?? ['keys' => []]);
        $store = new HmacKeyFsStore($this->baseDir.'/keys');
        $store->putJwks($tenant, $jwks);

        return new JsonResponse(['ok' => true], 200);
    }
}
