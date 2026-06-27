<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\Api;

use App\Rolling\Service\Http\Request\JsonPayloadReader;
use App\Rolling\ServiceInterface\Administration\RollingRoleHierarchyMutationServiceInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final readonly class HierarchyHttpService
{
    public function __construct(private RollingRoleHierarchyMutationServiceInterface $service, private JsonPayloadReader $payloadReader)
    {
    }

    public function review(Request $request): JsonResponse
    {
        return new JsonResponse($this->service->review($this->payloadReader->readObject($request)));
    }

    public function apply(Request $request): JsonResponse
    {
        $payload = $this->payloadReader->readObject($request);

        return new JsonResponse($this->service->apply($payload, [
            'actor' => (string) ($payload['actor'] ?? 'system'),
        ]));
    }
}
