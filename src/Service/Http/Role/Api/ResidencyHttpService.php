<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\Api;

use App\Rolling\DTO\Http\Role\ResidencyEnforcePayload;
use App\Rolling\Service\Http\Request\JsonPayloadReader;
use App\Rolling\Service\Residency\TenantDataResidencyGuardService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class ResidencyHttpService
{
    public function __construct(
        private readonly JsonPayloadReader $payloadReader,
        private readonly TenantDataResidencyGuardService $guard,
    ) {
    }

    public function enforce(Request $request): JsonResponse
    {
        $payload = ResidencyEnforcePayload::fromArray($this->payloadReader->readObject($request));
        $result = $this->guard->enforce($payload->tenant, $payload->attributes);

        return new JsonResponse($result + ['action' => $payload->action], 200);
    }
}
