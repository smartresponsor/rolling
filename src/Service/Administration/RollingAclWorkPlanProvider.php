<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclAdministrationReadinessReportProviderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclRemediationPlanProviderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclStorageReadinessReportProviderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclWorkPlanProviderInterface;
use App\Rolling\Value\Administration\RollingAclWorkPlan;

/**
 * Builds safe, actionable Rolling ACL work items for Administering aggregation.
 */
final readonly class RollingAclWorkPlanProvider implements RollingAclWorkPlanProviderInterface
{
    public function __construct(
        private RollingAclRemediationPlanProviderInterface $remediationPlanProvider,
        private RollingAclStorageReadinessReportProviderInterface $storageReadinessProvider,
        private RollingAclAdministrationReadinessReportProviderInterface $readinessReportProvider,
    ) {
    }

    public function plan(): RollingAclWorkPlan
    {
        $storage = $this->storageReadinessProvider->report()->toSafeArray();
        $readiness = $this->readinessReportProvider->report()->toSafeArray();
        $items = [];

        foreach ($this->remediationPlanProvider->plan()->items() as $remediationItem) {
            $items[] = [
                'key' => 'rolling.remediation.'.(string) $remediationItem['key'],
                'title' => (string) $remediationItem['title'],
                'stage' => 'hardening',
                'priority' => true === ($remediationItem['blocksMutations'] ?? false) ? 'high' : 'normal',
                'actionType' => 'remediation',
                'blocksMutation' => (bool) ($remediationItem['blocksMutations'] ?? false),
                'dependsOn' => [],
                'context' => [
                    'recommendation' => $remediationItem['recommendation'] ?? null,
                    'sourceSeverity' => $remediationItem['severity'] ?? 'info',
                    'sourceContext' => $remediationItem['context'] ?? [],
                ],
            ];
        }

        if (($storage['doctrineBacked'] ?? false) !== true) {
            $items[] = [
                'key' => 'rolling.acl_storage.promote_to_doctrine',
                'title' => 'Promote Rolling ACL storage from bootstrap to Doctrine-backed system storage.',
                'stage' => 'persistence',
                'priority' => 'high',
                'actionType' => 'implementation',
                'blocksMutation' => true,
                'dependsOn' => ['rolling.acl.entities.migrations'],
                'context' => [
                    'storageMode' => $storage['storageMode'] ?? 'unknown',
                    'expectedEntities' => $storage['expectedEntities'] ?? [],
                    'pendingCapabilities' => $storage['pendingCapabilities'] ?? [],
                ],
            ];
        }

        $items[] = [
            'key' => 'rolling.permission_catalog.keep_manifest_safe',
            'title' => 'Keep Rolling permission catalog and ACL manifests metadata-only for Administering.',
            'stage' => 'governance',
            'priority' => 'normal',
            'actionType' => 'policy',
            'blocksMutation' => false,
            'dependsOn' => [],
            'context' => [
                'supportedMutationTypes' => $readiness['supportedMutationTypes'] ?? [],
                'manifestSummary' => $readiness['manifestSummary'] ?? [],
                'forbiddenPayloads' => [
                    'subject_grants_dump',
                    'raw_policy_internals',
                    'secrets',
                    'sessions',
                    'passwords',
                ],
            ],
        ];

        return new RollingAclWorkPlan(new \DateTimeImmutable(), $items);
    }
}
