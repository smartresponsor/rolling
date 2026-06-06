<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\Api;

use App\Rolling\DTO\Http\Role\Obligation\ObligationApplyPayload;
use App\Rolling\DTO\Http\Role\Obligation\ObligationCheckApplyPayload;
use App\Rolling\Infrastructure\Audit\FileAuditTrail;
use App\Rolling\Infrastructure\Obligation\ObligationFsStore;
use App\Rolling\Infrastructure\Policy\PolicyFsStore;
use App\Rolling\Service\Http\Request\JsonPayloadReader;
use App\Rolling\Service\Obligation\PolicyObligationApplierService;
use App\Rolling\Service\Pipeline\DecisionPipeline;
use App\Rolling\Service\Pipeline\RollingPipelineRequestContext;
use App\Rolling\Service\Pipeline\Stage\ContextStage;
use App\Rolling\Service\Pipeline\Stage\PolicyStage;
use App\Rolling\Service\Pipeline\Stage\StrictDenyStage;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class ObligationHttpService
{
    public function __construct(
        private readonly JsonPayloadReader $payloadReader,
        private readonly string $baseDir = __DIR__.'/../../../../var',
    ) {
    }

    public function apply(Request $req): JsonResponse
    {
        $payload = ObligationApplyPayload::fromArray($this->payloadReader->readObject($req));
        $applier = new PolicyObligationApplierService(new ObligationFsStore($this->baseDir.'/policy'));
        $out = $applier->apply(
            $payload->tenant,
            $payload->relation,
            $payload->decision,
            $payload->attributes,
            $payload->resource,
            $payload->version,
        );

        return new JsonResponse($out, 200);
    }

    public function checkAndApply(Request $req): JsonResponse
    {
        $payload = ObligationCheckApplyPayload::fromArray($this->payloadReader->readObject($req));
        $decision = $this->evaluateDecision($payload->tenant, $payload->relation, $payload->subject, $payload->resource, $payload->attributes, $payload->version);

        $applier = new PolicyObligationApplierService(new ObligationFsStore($this->baseDir.'/policy'));
        $out = $applier->apply(
            $payload->tenant,
            $payload->relation,
            ['allowed' => $decision['allowed'], 'reason' => $decision['reason']],
            $payload->attributes,
            [] === $payload->resource ? null : $payload->resource,
            $payload->version,
        );
        $out['decision'] = $decision;

        $this->audit($payload->tenant, $payload->relation, $payload->subject, $decision, $payload->attributes, $payload->resource, $payload->version);

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

        $decision = $pipeline->evaluate(new RollingPipelineRequestContext(
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
