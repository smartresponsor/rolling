<?php

declare(strict_types=1);

namespace App\Rolling\Controller\Api;

use App\Rolling\Infrastructure\Audit\FileAuditTrail;
use App\Rolling\Infrastructure\Obligation\ObligationFsStore;
use App\Rolling\Infrastructure\Policy\PolicyFsStore;
use App\Rolling\Service\Obligation\ObligationApplier;
use App\Rolling\Service\Pipeline\DecisionPipeline;
use App\Rolling\Service\Pipeline\RequestContext;
use App\Rolling\Service\Pipeline\Stage\ContextStage;
use App\Rolling\Service\Pipeline\Stage\PolicyStage;
use App\Rolling\Service\Pipeline\Stage\StrictDenyStage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class ObligationController
{
    public function __construct(private readonly string $baseDir = __DIR__.'/../../../../var')
    {
    }

    public function apply(Request $req): JsonResponse
    {
        $payload = json_decode((string) $req->getContent(), true) ?? [];
        $tenant = (string) ($payload['tenant'] ?? 't1');
        $relation = (string) ($payload['relation'] ?? 'viewer');
        $decision = (array) ($payload['decision'] ?? ['allowed' => false]);
        $attrs = (array) ($payload['attrs'] ?? []);
        $resource = isset($payload['resource']) ? (array) $payload['resource'] : null;
        $version = (string) ($payload['version'] ?? 'active');

        $applier = new ObligationApplier(new ObligationFsStore($this->baseDir.'/policy'));
        $out = $applier->apply($tenant, $relation, $decision, $attrs, $resource, $version);

        return new JsonResponse($out, 200);
    }

    public function checkAndApply(Request $req): JsonResponse
    {
        $payload = json_decode((string) $req->getContent(), true) ?? [];
        $tenant = (string) ($payload['tenant'] ?? 't1');
        $relation = (string) ($payload['relation'] ?? 'viewer');
        $attrs = (array) ($payload['attrs'] ?? []);
        $resource = isset($payload['resource']) ? (array) $payload['resource'] : [];
        $version = (string) ($payload['version'] ?? 'active');
        $subject = (string) ($payload['subject'] ?? ($attrs['subject'] ?? 'anonymous'));

        $decision = $this->evaluateDecision($tenant, $relation, $subject, $resource, $attrs, $version);

        $applier = new ObligationApplier(new ObligationFsStore($this->baseDir.'/policy'));
        $out = $applier->apply(
            $tenant,
            $relation,
            ['allowed' => $decision['allowed'], 'reason' => $decision['reason']],
            $attrs,
            [] === $resource ? null : $resource,
            $version,
        );
        $out['decision'] = $decision;

        $this->audit($tenant, $relation, $subject, $decision, $attrs, $resource, $version);

        return new JsonResponse($out, 200);
    }

    /**
     * @param array<string,mixed> $resource
     * @param array<string,mixed> $attrs
     *
     * @return array{allowed:bool,reason:string,headers:array<int|string,mixed>,trace:array<int,array<string,mixed>>}
     */
    private function evaluateDecision(string $tenant, string $relation, string $subject, array $resource, array $attrs, string $version): array
    {
        $policyStore = new PolicyFsStore($this->baseDir.'/policy');
        $effective = 'active' === $version ? $policyStore->getEffective($tenant) : $policyStore->getDraft($tenant);
        $pipeline = new DecisionPipeline([
            new ContextStage(),
            new PolicyStage([$tenant => $effective]),
            new StrictDenyStage(),
        ]);

        $decision = $pipeline->evaluate(new RequestContext(
            tenant: $tenant,
            subject: $subject,
            action: $relation,
            resource: $resource,
            attrs: $attrs,
        ));

        return [
            'allowed' => $decision->allow,
            'reason' => $decision->reason,
            'headers' => $decision->headers,
            'trace' => $decision->explain,
        ];
    }

    /**
     * @param array<string,mixed> $decision
     * @param array<string,mixed> $attrs
     * @param array<string,mixed> $resource
     */
    private function audit(string $tenant, string $relation, string $subject, array $decision, array $attrs, array $resource, string $version): void
    {
        $trail = new FileAuditTrail($this->baseDir.'/audit');
        $trail->write([
            'type' => 'obligation.check_and_apply',
            'tenant' => $tenant,
            'relation' => $relation,
            'subject' => $subject,
            'version' => $version,
            'decision' => $decision,
            'attrs' => $attrs,
            'resource' => $resource,
        ]);
    }
}
