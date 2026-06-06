<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\Api;

use App\Rolling\DTO\Http\Role\Sod\SodCheckPayload;
use App\Rolling\Service\Http\Request\JsonPayloadReader;
use App\Rolling\Service\Sod\SeparationOfDutiesGuardService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class SodHttpService
{
    public function __construct(
        private readonly JsonPayloadReader $payloadReader,
        private readonly SeparationOfDutiesGuardService $guard,
    ) {
    }

    /**
     * @param Request $r
     *
     * @return JsonResponse
     */
    public function check(Request $r): JsonResponse
    {
        $payload = SodCheckPayload::fromArray($this->payloadReader->readObject($r));

        return new JsonResponse($this->guard->validate($payload->attributes), 200);
    }
}
