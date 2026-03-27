<?php

declare(strict_types=1);

namespace Http\Role\Api;

use App\Infra\Role\Audit\AuditFsPort;
use App\Infra\Role\Context\RequestContextProvider;
use App\Infra\Role\Obligation\ObligationFsStore;
use App\Infra\Role\Policy\PolicyFsStore;
use App\Service\Role\Engine\{DecisionPipeline, PolicyEngine};
use App\Service\Role\Obligation\ObligationApplier;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

// optional engine for /v2/check/oblige if available

/**
 *
 */

/**
 *
 */
final class ObligationController
{
    /**
     * @param string $baseDir
     */
    public function __construct(private readonly string $baseDir = __DIR__ . '/../../../../var') {}

    // Apply only (no decision); expects {tenant, relation, decision:{allowed}, attrs, resource?, version?}

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function apply(Request $req): JsonResponse
    {
        $p = json_decode((string) $req->getContent(), true) ?? [];
        $tenant = (string) ($p['tenant'] ?? 't1');
        $relation = (string) ($p['relation'] ?? 'viewer');
        $decision = (array) ($p['decision'] ?? ['allowed' => false]);
        $attrs = (array) ($p['attrs'] ?? []);
        $resource = isset($p['resource']) ? (array) $p['resource'] : null;
        $version = (string) ($p['version'] ?? 'active');
        $applier = new ObligationApplier(new ObligationFsStore($this->baseDir . '/policy'));
        $out = $applier->apply($tenant, $relation, $decision, $attrs, $resource, $version);
        return new JsonResponse($out, 200);
    }

    // Full: decision + apply (if engine available); {tenant, relation, attrs, resource?, version?}

    /**
     * @param \Symfony\Component\HttpFoundation\Request $req
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function checkAndApply(Request $req): JsonResponse
    {
        $p = json_decode((string) $req->getContent(), true) ?? [];
        $tenant = (string) ($p['tenant'] ?? 't1');
        $relation = (string) ($p['relation'] ?? 'viewer');
        $attrs = (array) ($p['attrs'] ?? []);
        $resource = isset($p['resource']) ? (array) $p['resource'] : null;
        $version = (string) ($p['version'] ?? 'active');

        if (!class_exists(PolicyEngine::class)) {
            return new JsonResponse(['error' => 'PolicyEngine not available in this package; use /v2/obligations/apply or merge with E2/E3'], 501);
        }
        $engine = new PolicyEngine(new PolicyFsStore($this->baseDir . '/policy'));
        $pipe = new DecisionPipeline($engine, new RequestContextProvider(['now' => gmdate('c')]), new AuditFsPort($this->baseDir . '/audit/decisions.ndjson'));
        $decision = $pipe->run($tenant, $relation, $attrs, $version);
        $applier = new ObligationApplier(new ObligationFsStore($this->baseDir . '/policy'));
        $out = $applier->apply($tenant, $relation, is_array($decision) ? $decision : ['allowed' => false], $attrs, $resource, $version);
        $out['decision'] = $decision;
        return new JsonResponse($out, 200);
    }
}
