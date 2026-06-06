<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\Api;

use App\Rolling\DTO\Http\Role\RoleCheckPayload;
use App\Rolling\Service\Audit\DecisionAuditLogWriter;
use App\Rolling\Service\Consistency\Http\ConsistencyHeaderService;
use App\Rolling\Service\Explain\RelationshipTupleLogReader;
use App\Rolling\Service\Http\Request\JsonPayloadReader;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class CheckHttpService
{
    /**
     * @param string $tuplesPath
     * @param string $logDir
     */
    public function __construct(
        private readonly JsonPayloadReader $payloadReader,
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
        $payload = RoleCheckPayload::fromArray($this->payloadReader->readObject($req));

        $mode = ConsistencyHeaderService::mode($req);
        $reader = new RelationshipTupleLogReader($this->tuplesPath);
        $evidence = $reader->exists($payload->tenant, $payload->subject, $payload->relation, $payload->resource);
        $allowed = null !== $evidence;
        $token = (string) @filesize($this->tuplesPath) ?: '0';

        // audit
        $logger = new DecisionAuditLogWriter($this->logDir);
        $auditEvent = [
            'ts' => gmdate('c'),
            'tenant' => $payload->tenant,
            'subject' => $payload->subject,
            'relation' => $payload->relation,
            'resource' => $payload->resource,
            'context' => $payload->context,
            'effect' => $allowed ? 'allow' : 'deny',
            'reason' => $allowed ? 'evidence' : 'no-tuple',
            'consistency' => $mode,
            'token' => $token,
        ];
        $meta = $logger->write($auditEvent, $payload->obligations);

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
