<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\Api;

use App\Rolling\DTO\Http\Role\PolicyDraftPayload;
use App\Rolling\DTO\Http\Role\PolicyPublishPayload;
use App\Rolling\Service\Http\Request\JsonPayloadReader;
use App\Rolling\ServiceInterface\PolicyStoreInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class PolicyHttpService
{
    public function __construct(
        private readonly JsonPayloadReader $payloadReader,
        private readonly PolicyStoreInterface $policyStore,
    ) {
    }

    public function getDraft(Request $request): JsonResponse
    {
        $tenant = (string) ($request->query->get('tenant') ?? 't1');

        return new JsonResponse(['draft' => $this->policyStore->getDraft($tenant)], 200);
    }

    public function putDraft(Request $request): JsonResponse
    {
        $payload = PolicyDraftPayload::fromArray($this->payloadReader->readObject($request));
        $this->policyStore->putDraft($payload->tenant, $payload->expression);

        return new JsonResponse(['ok' => true], 200);
    }

    public function publish(Request $request): JsonResponse
    {
        $payload = PolicyPublishPayload::fromArray($this->payloadReader->readObject($request));
        $version = $this->policyStore->publish($payload->tenant, $payload->note);

        return new JsonResponse(['version' => $version], 200);
    }

    public function getEffective(Request $request): JsonResponse
    {
        $tenant = (string) ($request->query->get('tenant') ?? 't1');

        return new JsonResponse(['expr' => $this->policyStore->getEffective($tenant)], 200);
    }
}
