<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\Api;

use App\Rolling\DTO\Http\Role\Pipeline\WhatIfPayload;
use App\Rolling\Service\Http\Request\JsonPayloadReader;
use App\Rolling\Service\Pipeline\DecisionPipeline;
use App\Rolling\Service\Pipeline\RollingPipelineRequestContext;
use App\Rolling\Service\Pipeline\Stage\ContextStage;
use App\Rolling\Service\Pipeline\Stage\StrictDenyStage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class WhatIfHttpService
{
    public function __construct(private readonly JsonPayloadReader $payloadReader)
    {
    }

    public function run(Request $r): JsonResponse
    {
        $payload = WhatIfPayload::fromArray($this->payloadReader->readObject($r));
        $attrs = $payload->attributes;
        foreach ($payload->hypothesis as $key => $value) {
            $attrs[(string) $key] = $value;
        }

        $ctx = new RollingPipelineRequestContext($payload->tenant, $payload->subject, $payload->action, $payload->resource, $attrs);
        $pipe = new DecisionPipeline([new ContextStage(), new StrictDenyStage()]);
        $d = $pipe->evaluate($ctx);

        return new JsonResponse(['allow' => $d->allow, 'reason' => $d->reason, 'explain' => $d->explain], 200);
    }
}
