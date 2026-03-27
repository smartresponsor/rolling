<?php

declare(strict_types=1);

namespace Http\Role\Api;

use App\Domain\Role\Model\RequestContext;
use Pipeline\DecisionPipeline;
use Pipeline\Stage\{PolicyStage};
use Pipeline\Stage\ContextStage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */

/**
 *
 */
final class PelEvalController
{
    /**
     * @return \Pipeline\DecisionPipeline
     */
    private function pipe(): DecisionPipeline
    {
        $pol = ['t1' => "(subject.role in ['admin','editor']) and (action == 'write' or action == 'read')"];
        return new DecisionPipeline([new ContextStage(), new PolicyStage($pol)]);
    }

    public function eval(Request $req): JsonResponse
    {
        $p = json_decode((string) $req->getContent(), true) ?? [];
        $ctx = new RequestContext((string) ($p['tenant'] ?? 't1'), (string) ($p['subject'] ?? 'u1'), (string) ($p['action'] ?? 'read'), (array) ($p['resource'] ?? []), (array) ($p['attrs'] ?? []));
        $d = $this->pipe()->evaluate($ctx);
        return new JsonResponse(['allow' => $d->allow, 'reason' => $d->reason, 'explain' => $d->explain], 200);
    }
}
