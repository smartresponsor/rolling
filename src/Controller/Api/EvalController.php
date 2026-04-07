<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Legacy\Model\RequestContext;
use App\Service\Pipeline\DecisionPipeline;
use App\Service\Pipeline\Stage\{StrictDenyStage};
use App\Service\Pipeline\Stage\ContextStage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */

/**
 *
 */
final class EvalController
{
    /**
     * @return \Pipeline\DecisionPipeline
     */
    private function pipe(): DecisionPipeline
    {
        return new DecisionPipeline([new ContextStage(), new StrictDenyStage()]);
    }

    public function eval(Request $req): JsonResponse
    {
        $p = json_decode((string)$req->getContent(), true) ?? [];
        $ctx = new RequestContext((string)($p['tenant'] ?? 't1'), (string)($p['subject'] ?? 'u1'), (string)($p['action'] ?? 'read'), (array)($p['resource'] ?? []), (array)($p['attrs'] ?? []));
        $d = $this->pipe()->evaluate($ctx);
        return new JsonResponse(['allow' => $d->allow, 'reason' => $d->reason, 'headers' => $d->headers, 'explain' => $d->explain], 200);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function evalBatch(Request $req): JsonResponse
    {
        $p = json_decode((string)$req->getContent(), true) ?? [];
        $list = (array)($p['list'] ?? []);
        $res = [];
        foreach ($list as $row) {
            $ctx = new RequestContext((string)($row['tenant'] ?? 't1'), (string)($row['subject'] ?? 'u1'), (string)($row['action'] ?? 'read'), (array)($row['resource'] ?? []), (array)($row['attrs'] ?? []));
            $d = $this->pipe()->evaluate($ctx);
            $res[] = ['allow' => $d->allow, 'reason' => $d->reason];
        }
        return new JsonResponse(['items' => $res], 200);
    }
}
