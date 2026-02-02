<?php
declare(strict_types=1);

namespace App\Integration\Symfony\Controller;

use Http\Role\V2\ApiV2;
use Http\Role\V2\ApiV2Batch;
use PolicyInterface\Role\PdpV2Interface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */

/**
 *
 */
final class RoleApiV2Controller
{
    private ApiV2 $api;
    private ApiV2Batch $batch;

    /**
     * @param \PolicyInterface\Role\PdpV2Interface $pdp
     */
    public function __construct(PdpV2Interface $pdp)
    {
        $this->api = new ApiV2($pdp);
        $this->batch = new ApiV2Batch($pdp);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function check(Request $req): JsonResponse
    {
        $json = json_decode($req->getContent() ?: '{}', true);
        $resp = $this->api->check((array)$json);
        return new JsonResponse(json_decode($resp->body, true), $resp->status, $resp->headers);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function checkBatch(Request $req): JsonResponse
    {
        $json = json_decode($req->getContent() ?: '{}', true);
        $resp = $this->batch->checkBatch((array)$json);
        return new JsonResponse(json_decode($resp->body, true), $resp->status, $resp->headers);
    }
}
