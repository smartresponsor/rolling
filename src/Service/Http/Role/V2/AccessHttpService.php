<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\V2;

use App\Rolling\DTO\Http\Role\AccessCheckPayload;
use App\Rolling\Integration\Http\V2\ApiV2;
use App\Rolling\Integration\Http\V2\ApiV2Batch;
use App\Rolling\Service\Http\Request\JsonPayloadReader;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class AccessHttpService
{
    private ApiV2 $api;
    private ApiV2Batch $batch;

    public function __construct(PdpV2Interface $pdp, private readonly JsonPayloadReader $payloadReader)
    {
        $this->api = new ApiV2($pdp);
        $this->batch = new ApiV2Batch($pdp);
    }

    public function check(Request $req): JsonResponse
    {
        $payload = AccessCheckPayload::fromArray($this->payloadReader->readObject($req));
        $resp = $this->api->check($payload->payload);

        return new JsonResponse(json_decode($resp->body, true), $resp->status, $resp->headers);
    }

    public function checkBatch(Request $req): JsonResponse
    {
        $payload = AccessCheckPayload::fromArray($this->payloadReader->readObject($req));
        $resp = $this->batch->checkBatch($payload->payload);

        return new JsonResponse(json_decode($resp->body, true), $resp->status, $resp->headers);
    }
}
