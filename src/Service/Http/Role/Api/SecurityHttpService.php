<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\Api;

use App\Rolling\DTO\Http\Role\SecurityJwksPayload;
use App\Rolling\DTO\Http\Role\SecurityRotatePayload;
use App\Rolling\DTO\Http\Role\SecuritySignPayload;
use App\Rolling\DTO\Http\Role\SecurityVerifyPayload;
use App\Rolling\InfrastructureInterface\Security\JwksVerifierInterface;
use App\Rolling\InfrastructureInterface\Security\KeyStoreInterface;
use App\Rolling\Service\Http\Request\JsonPayloadReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class SecurityHttpService
{
    public function __construct(
        private readonly JsonPayloadReader $payloadReader,
        private readonly KeyStoreInterface $keyStore,
        private readonly JwksVerifierInterface $verifier,
    ) {
    }

    public function sign(Request $request): JsonResponse
    {
        $payload = SecuritySignPayload::fromArray($this->payloadReader->readObject($request));
        $jwt = $this->verifier->signHs256($payload->tenant, $payload->claims);

        return new JsonResponse(['jwt' => $jwt], 200);
    }

    public function verify(Request $request): JsonResponse
    {
        $payload = SecurityVerifyPayload::fromArray($this->payloadReader->readObject($request));
        $result = $this->verifier->verify($payload->tenant, $payload->token);

        return new JsonResponse($result, 200);
    }

    public function rotate(Request $request): JsonResponse
    {
        $payload = SecurityRotatePayload::fromArray($this->payloadReader->readObject($request));
        $kid = $this->keyStore->rotateHmac($payload->tenant, $payload->note);

        return new JsonResponse(['kid' => $kid], 200);
    }

    public function jwksGet(Request $request): JsonResponse
    {
        $tenant = (string) ($request->query->get('tenant') ?? 't1');

        return new JsonResponse($this->keyStore->jwks($tenant), 200);
    }

    public function jwksPut(Request $request): JsonResponse
    {
        $payload = SecurityJwksPayload::fromArray($this->payloadReader->readObject($request));
        $this->keyStore->putJwks($payload->tenant, $payload->jwks);

        return new JsonResponse(['ok' => true], 200);
    }
}
