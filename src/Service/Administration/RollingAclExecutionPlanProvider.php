<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclExecutionPlanProviderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclStorageReadinessReportProviderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclWorkPlanProviderInterface;
use App\Rolling\Value\Administration\RollingAclExecutionPlan;

/**
 * Converts Rolling ACL work/readiness metadata into safe execution-planning steps.
 */
final readonly class RollingAclExecutionPlanProvider implements RollingAclExecutionPlanProviderInterface
{
    public function __construct(
        private RollingAclWorkPlanProviderInterface $workPlanProvider,
        private RollingAclStorageReadinessReportProviderInterface $storageReadinessProvider,
    ) {
    }

    public function plan(): RollingAclExecutionPlan
    {
        $storage = $this->storageReadinessProvider->report()->toSafeArray();
        $steps = [];

        foreach ($this->workPlanProvider->plan()->items() as $item) {
            $blocksMutation = (bool) ($item['blocksMutation'] ?? false);
            $steps[] = [
                'key' => 'execute.'.(string) $item['key'],
                'title' => (string) $item['title'],
                'component' => 'Rolling',
                'stage' => (string) $item['stage'],
                'executionType' => $blocksMutation ? 'manual_implementation' : 'policy_guard',
                'blocked' => $blocksMutation,
                'requiresReview' => true,
                'safeToAutomate' => false,
                'sourceWorkItem' => (string) $item['key'],
                'context' => [
                    'storageMode' => $storage['storageMode'] ?? 'unknown',
                    'doctrineBacked' => $storage['doctrineBacked'] ?? false,
                    'actionType' => $item['actionType'] ?? 'unknown',
                    'dependsOn' => $item['dependsOn'] ?? [],
                    'note' => $blocksMutation
                        ? 'Rolling must own this implementation before Administering can execute real ACL mutations.'
                        : 'This is a metadata/governance guard and may be displayed without exposing subject grants.',
                ],
            ];
        }

        return new RollingAclExecutionPlan(
            new \DateTimeImmutable(),
            $steps,
            [
                'Rolling owns roles, permissions, ACL rules, policy decisions and execution audit.',
                'Administering may launch only review/apply requests that Rolling validates and executes.',
                'No execution step may expose raw subject grants, policy internals, secrets, sessions, or passwords.',
            ],
        );
    }
}
