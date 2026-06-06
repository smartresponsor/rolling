<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\Api;

use App\Rolling\DTO\Http\Role\Pipeline\PipelineEvalPayload;
use App\Rolling\Service\Http\Request\JsonPayloadReader;
use App\Rolling\Service\Pipeline\DecisionPipeline;
use App\Rolling\Service\Pipeline\RollingPipelineRequestContext;
use App\Rolling\Service\Pipeline\Stage\ContextStage;
use App\Rolling\Service\Pipeline\Stage\PolicyStage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class PelEvalHttpService
{
    public function __construct(private readonly JsonPayloadReader $payloadReader)
    {
    }

    private function pipe(): DecisionPipeline
    {
        $policies = [
            't1' => "(subject.role in ['admin','editor']) and (action == 'write' or action == 'read')",
        ];

        return new DecisionPipeline([new ContextStage(), new PolicyStage($policies)]);
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
            'explain' => $d->explain,
        ], 200);
    }
}
