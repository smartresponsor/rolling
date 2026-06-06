<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\Api;

use App\Rolling\DTO\Http\Role\Pipeline\PipelineEvalBatchPayload;
use App\Rolling\DTO\Http\Role\Pipeline\PipelineEvalPayload;
use App\Rolling\Service\Http\Request\JsonPayloadReader;
use App\Rolling\Service\Pipeline\DecisionPipeline;
use App\Rolling\Service\Pipeline\RollingPipelineRequestContext;
use App\Rolling\Service\Pipeline\Stage\ContextStage;
use App\Rolling\Service\Pipeline\Stage\StrictDenyStage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class EvalHttpService
{
    public function __construct(private readonly JsonPayloadReader $payloadReader)
    {
    }

    private function pipe(): DecisionPipeline
    {
        return new DecisionPipeline([new ContextStage(), new StrictDenyStage()]);
    }

    public function eval(Request $req): JsonResponse
    {
        $payload = PipelineEvalPayload::fromArray($this->payloadReader->readObject($req));
        $ctx = new RollingPipelineRequestContext(
            $payload->tenant,
            $payload->subject,
            $payload->action,
            $payload->resource,
            $payload->attributes,
        );
        $d = $this->pipe()->evaluate($ctx);

        return new JsonResponse([
            'allow' => $d->allow,
            'reason' => $d->reason,
            'headers' => $d->headers,
            'explain' => $d->explain,
        ], 200);
    }

    public function evalBatch(Request $req): JsonResponse
    {
        $payload = PipelineEvalBatchPayload::fromArray($this->payloadReader->readObject($req));
        $res = [];

        foreach ($payload->items as $item) {
            $ctx = new RollingPipelineRequestContext(
                $item->tenant,
                $item->subject,
                $item->action,
                $item->resource,
                $item->attributes,
            );
            $d = $this->pipe()->evaluate($ctx);
            $res[] = ['allow' => $d->allow, 'reason' => $d->reason];
        }

        return new JsonResponse(['items' => $res], 200);
    }
}
