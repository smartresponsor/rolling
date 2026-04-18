<?php

declare(strict_types=1);

namespace App\Controller\V2;

use App\Service\Shadow\Diff\DecisionDiff;
use App\ServiceInterface\Policy\PdpV2Interface;
use App\Entity\Role\Scope;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\SubjectId;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 */

/**
 *
 */
final class DebugPolicyShadowController
{
    /**
     * @param \PolicyInterface\Role\PdpV2Interface $live
     * @param \PolicyInterface\Role\PdpV2Interface $shadow
     */
    public function __construct(private readonly PdpV2Interface $live, private readonly PdpV2Interface $shadow) {}

    /**
     * @param \Symfony\Component\HttpFoundation\Request $r
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function compare(Request $r): JsonResponse
    {
        $in = json_decode((string) $r->getContent(), true) ?? [];
        $s = new SubjectId((string) ($in['subject'] ?? ''));
        $a = new PermissionKey((string) ($in['action'] ?? ''));
        $sc = (array) ($in['scope'] ?? []);
        $scope = match ((string) ($sc['type'] ?? 'global')) {
            'tenant' => Scope::tenant((string) ($sc['tenantId'] ?? '')),
            'resource' => Scope::resource((string) ($sc['resourceId'] ?? ''), (string) ($sc['key'] ?? 'resource'), isset($sc['tenantId']) ? (string) $sc['tenantId'] : null),
            default => Scope::global(),
        };
        $ctx = (array) ($in['context'] ?? []);
        $live = $this->live->check($s, $a, $scope, $ctx);
        $shadow = $this->shadow->check($s, $a, $scope, $ctx);
        $diff = DecisionDiff::diff($live, $shadow);
        return new JsonResponse(['live' => ['allow' => $live->isAllow(), 'reason' => $live->reason(), 'obligations' => $live->obligations()->toArray(),], 'shadow' => ['allow' => $shadow->isAllow(), 'reason' => $shadow->reason(), 'obligations' => $shadow->obligations()->toArray(),], 'diff' => $diff,]);
    }
}
