<?php

declare(strict_types=1);

namespace App\Rolling\Controller\Api;

use App\Rolling\Service\Audit\DecisionAuditLogWriter;
use App\Rolling\Service\Consistency\Http\ConsistencyHeaderService;
use App\Rolling\Service\Explain\RelationshipTupleLogReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class CheckController
{
    /**
     * @param string $tuplesPath
     * @param string $logDir
     */
    public function __construct(
        private readonly string $tuplesPath = __DIR__.'/../../../../var/tuples.ndjson',
        private readonly string $logDir = __DIR__.'/../../../../var/log/role',
    ) {
    }

    /**
     * @param Request $req
     *
     * @return JsonResponse
     */
    public function check(Request $req): JsonResponse
    {
        $payload = json_decode((string) $req->getContent(), true) ?? [];
        $tenant = (string) ($payload['tenant'] ?? 't1');
        $subject = (string) ($payload['subject'] ?? '');
        $relation = (string) ($payload['relation'] ?? '');
        $resource = (string) ($payload['resource'] ?? '');
        $context = is_array($payload['context'] ?? null) ? $payload['context'] : [];
        $oblig = is_array($payload['obligations'] ?? null) ? $payload['obligations'] : [];

        $mode = ConsistencyHeaderService::mode($req);
        $reader = new RelationshipTupleLogReader($this->tuplesPath);
        $evidence = $reader->exists($tenant, $subject, $relation, $resource);
        $allowed = null !== $evidence;
        $token = (string) @filesize($this->tuplesPath) ?: '0';

        // audit
        $logger = new DecisionAuditLogWriter($this->logDir);
        $auditEvent = [
            'ts' => gmdate('c'),
            'tenant' => $tenant,
            'subject' => $subject,
            'relation' => $relation,
            'resource' => $resource,
            'context' => $context,
            'effect' => $allowed ? 'allow' : 'deny',
            'reason' => $allowed ? 'evidence' : 'no-tuple',
            'consistency' => $mode,
            'token' => $token,
        ];
        $meta = $logger->write($auditEvent, $oblig);

        $out = [
            'allowed' => $allowed,
            'meta' => [
                'consistency' => $mode,
                'token' => $token,
                'evidence' => $evidence,
                'audit' => $meta['meta'] ?? $meta,
            ],
        ];
        $res = new JsonResponse($out, 200);
        ConsistencyHeaderService::applyHeaders($res, $mode, $token);

        return $res;
    }
}
