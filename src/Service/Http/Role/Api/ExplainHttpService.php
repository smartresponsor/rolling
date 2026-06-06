<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\Api;

use App\Rolling\DTO\Http\Role\Pipeline\PipelineEvalPayload;
use App\Rolling\Service\Explain\DecisionGraph;
use App\Rolling\Service\Http\Request\JsonPayloadReader;
use App\Rolling\Service\Pipeline\RollingPipelineRequestContext;
use App\Rolling\Service\Pipeline\RollingPipelineTrace;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class ExplainHttpService
{
    public function __construct(private readonly JsonPayloadReader $payloadReader)
    {
    }

    /**
     * @param Request $req
     *
     * @return JsonResponse
     */
    public function explain(Request $req): JsonResponse
    {
        $payload = PipelineEvalPayload::fromArray($this->payloadReader->readObject($req));
        new RollingPipelineRequestContext($payload->tenant, $payload->subject, $payload->action, $payload->resource, $payload->attributes);
        $trace = new RollingPipelineTrace();
        $trace->add('context', 'normalized');
        $trace->add('policy', 'no');
        $graph = DecisionGraph::build($trace);

        return new JsonResponse($graph, 200);
    }
}
