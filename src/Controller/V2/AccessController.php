<?php

declare(strict_types=1);

namespace App\Controller\V2;

use App\Integration\Http\V2\ApiV2;
use App\Integration\Http\V2\ApiV2Batch;
use App\ServiceInterface\Policy\PdpV2Interface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class AccessController
{
    private ApiV2 $api;
    private ApiV2Batch $batch;

    public function __construct(PdpV2Interface $pdp)
    {
        $this->api = new ApiV2($pdp);
        $this->batch = new ApiV2Batch($pdp);
    }

    public function check(Request $req): JsonResponse
    {
        $json = json_decode($req->getContent() ?: '{}', true);
        $resp = $this->api->check((array) $json);

        return new JsonResponse(json_decode($resp->body, true), $resp->status, $resp->headers);
    }

    public function checkBatch(Request $req): JsonResponse
    {
        $json = json_decode($req->getContent() ?: '{}', true);
        $resp = $this->batch->checkBatch((array) $json);

        return new JsonResponse(json_decode($resp->body, true), $resp->status, $resp->headers);
    }
}
