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
final class WhatIfController
{
    /**
     * @param \Symfony\Component\HttpFoundation\Request $r
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function run(Request $r): JsonResponse
    {
        $p = json_decode((string) $r->getContent(), true) ?? [];
        $ctx = new RequestContext((string) ($p['tenant'] ?? 't1'), (string) ($p['subject'] ?? 'u1'), (string) ($p['action'] ?? 'read'), (array) ($p['resource'] ?? []), (array) ($p['attrs'] ?? []));
        $hyp = (array) ($p['hyp'] ?? []);
        foreach ($hyp as $k => $v) {
            $ctx->attrs[$k] = $v;
        }
        $pipe = new DecisionPipeline([new ContextStage(), new StrictDenyStage()]);
        $d = $pipe->evaluate($ctx);
        return new JsonResponse(['allow' => $d->allow, 'reason' => $d->reason, 'explain' => $d->explain], 200);
    }
}
