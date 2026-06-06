<?php

declare(strict_types=1);

namespace App\Rolling\Service\Http\Role\V2;

use App\Rolling\DTO\Http\Role\Debug\DebugPolicyShadowPayload;
use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Service\Http\Request\JsonPayloadReader;
use App\Rolling\Service\Shadow\Diff\DecisionDiff;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

final class DebugPolicyShadowHttpService
{
    public function __construct(private readonly PdpV2Interface $live, private readonly PdpV2Interface $shadow, private readonly JsonPayloadReader $payloadReader)
    {
    }

    public function compare(Request $r): JsonResponse
    {
        $payload = DebugPolicyShadowPayload::fromArray($this->payloadReader->readObject($r));
        $s = new SubjectId($payload->subject);
        $a = new PermissionKey($payload->action);
        $sc = $payload->scope;
        $scope = match ((string) ($sc['type'] ?? 'global')) {
            'tenant' => Scope::tenant((string) ($sc['tenantId'] ?? '')),
            'resource' => Scope::resource((string) ($sc['resourceId'] ?? ''), (string) ($sc['key'] ?? 'resource'), isset($sc['tenantId']) ? (string) $sc['tenantId'] : null),
            default => Scope::global(),
        };
        $live = $this->live->check($s, $a, $scope, $payload->context);
        $shadow = $this->shadow->check($s, $a, $scope, $payload->context);
        $diff = DecisionDiff::diff($live, $shadow);

        return new JsonResponse(['live' => ['allow' => $live->isAllow(), 'reason' => $live->reason(), 'obligations' => $live->obligations()->toArray()], 'shadow' => ['allow' => $shadow->isAllow(), 'reason' => $shadow->reason(), 'obligations' => $shadow->obligations()->toArray()], 'diff' => $diff]);
    }
}
