<?php

declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\Pipeline\DecisionPipeline;
use App\Service\Pipeline\RequestContext;
use App\Service\Pipeline\Stage\ContextStage;
use App\Service\Pipeline\Stage\PolicyStage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class PelEvalController
{
    private function pipe(): DecisionPipeline
    {
        $policies = [
            't1' => "(subject.role in ['admin','editor']) and (action == 'write' or action == 'read')",
        ];

        return new DecisionPipeline([new ContextStage(), new PolicyStage($policies)]);
    }

    public function eval(Request $req): JsonResponse
    {
        $p = json_decode((string) $req->getContent(), true) ?? [];
        $ctx = new RequestContext(
            (string) ($p['tenant'] ?? 't1'),
            (string) ($p['subject'] ?? 'u1'),
            (string) ($p['action'] ?? 'read'),
            (array) ($p['resource'] ?? []),
            (array) ($p['attrs'] ?? []),
        );
        $d = $this->pipe()->evaluate($ctx);

        return new JsonResponse([
            'allow' => $d->allow,
            'reason' => $d->reason,
            'explain' => $d->explain,
        ], 200);
    }
}
