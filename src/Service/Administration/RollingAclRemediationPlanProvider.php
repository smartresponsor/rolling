<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclMutationValidatorInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclRemediationPlanProviderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclStorageReadinessReportProviderInterface;
use App\Rolling\Value\Administration\RollingAclRemediationPlan;

/**
 * Builds safe next-step guidance for Rolling ACL hardening.
 */
final readonly class RollingAclRemediationPlanProvider implements RollingAclRemediationPlanProviderInterface
{
    public function __construct(
        private RollingAclStorageReadinessReportProviderInterface $storageReadinessProvider,
        private RollingAclMutationValidatorInterface $mutationValidator,
    ) {
    }

    public function plan(): RollingAclRemediationPlan
    {
        $storage = $this->storageReadinessProvider->report()->toSafeArray();
        $items = [];

        if (($storage['doctrineBacked'] ?? false) !== true) {
            $items[] = [
                'key' => 'rolling.storage.bootstrap',
                'severity' => 'warning',
                'title' => 'Rolling ACL storage is still bootstrap-mode.',
                'recommendation' => 'Promote RollingRole, RollingPermission, role-permission, subject-role, hierarchy, ACL rule, and execution event models to Doctrine-backed storage before enabling real mutations.',
                'blocksMutations' => true,
                'context' => [
                    'storageMode' => $storage['storageMode'] ?? 'unknown',
                    'expectedEntities' => $storage['expectedEntities'] ?? [],
                    'pendingCapabilities' => $storage['pendingCapabilities'] ?? [],
                ],
            ];
        }

        $allowedTypes = $this->mutationValidator->allowedMutationTypes();
        if ([] === $allowedTypes) {
            $items[] = [
                'key' => 'rolling.mutation.types.empty',
                'severity' => 'error',
                'title' => 'Rolling exposes no allowed ACL mutation types.',
                'recommendation' => 'Declare a minimal mutation catalog before Administering builds apply forms.',
                'blocksMutations' => true,
                'context' => [],
            ];
        }

        if ([] === $items) {
            $items[] = [
                'key' => 'rolling.ready',
                'severity' => 'info',
                'title' => 'Rolling ACL administration is ready for Administering orchestration.',
                'recommendation' => 'Keep mutation execution inside Rolling and continue exposing only safe manifests, reviews, execution reports, and result metadata to Administering.',
                'blocksMutations' => false,
                'context' => [
                    'allowedMutationTypes' => $allowedTypes,
                ],
            ];
        }

        return new RollingAclRemediationPlan(new \DateTimeImmutable(), $items);
    }
}
