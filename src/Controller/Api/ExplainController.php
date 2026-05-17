<?php

declare(strict_types=1);

namespace App\Rolling\Controller\Api;

use App\Rolling\Service\Explain\DecisionGraph;
use App\Rolling\Service\Pipeline\DecisionPipeline;
use App\Rolling\Service\Pipeline\RollingPipelineRequestContext;
use App\Rolling\Service\Pipeline\RollingPipelineTrace;
use App\Rolling\Service\Pipeline\Stage\ContextStage;
use App\Rolling\Service\Pipeline\Stage\StrictDenyStage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class ExplainController
{
    /**
     * @param Request $req
     *
     * @return JsonResponse
     */
    public function explain(Request $req): JsonResponse
    {
        $p = json_decode((string) $req->getContent(), true) ?? [];
        $ctx = new RollingPipelineRequestContext((string) ($p['tenant'] ?? 't1'), (string) ($p['subject'] ?? 'u1'), (string) ($p['action'] ?? 'read'), (array) ($p['resource'] ?? []), (array) ($p['attrs'] ?? []));
        $pipe = new DecisionPipeline([new ContextStage(), new StrictDenyStage()]);
        // rerun to capture Trace (we don't expose pipeline internals here; mimic)
        $trace = new RollingPipelineTrace();
        $trace->add('context', 'normalized');
        $trace->add('policy', 'no');
        $graph = DecisionGraph::build($trace);

        return new JsonResponse($graph, 200);
    }
}
