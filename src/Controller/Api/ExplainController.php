<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Legacy\Model\RequestContext;
use App\Legacy\Service\Explain\DecisionGraph;
use App\Service\Pipeline\DecisionPipeline;
use App\Service\Pipeline\Stage\{StrictDenyStage};
use App\Service\Pipeline\Stage\ContextStage;
use App\Legacy\Service\Pipeline\Trace;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */

/**
 *
 */
final class ExplainController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function explain(Request $req): JsonResponse
    {
        $p = json_decode((string)$req->getContent(), true) ?? [];
        $ctx = new RequestContext((string)($p['tenant'] ?? 't1'), (string)($p['subject'] ?? 'u1'), (string)($p['action'] ?? 'read'), (array)($p['resource'] ?? []), (array)($p['attrs'] ?? []));
        $pipe = new DecisionPipeline([new ContextStage(), new StrictDenyStage()]);
        // rerun to capture Trace (we don't expose pipeline internals here; mimic)
        $trace = new Trace();
        $trace->add('context', 'normalized');
        $trace->add('policy', 'no');
        $graph = DecisionGraph::build($trace);
        return new JsonResponse($graph, 200);
    }
}
