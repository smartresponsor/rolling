<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclAdministrationContractMatrixProviderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclCapabilityMatrixProviderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclHealthReportProviderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclStorageReadinessReportProviderInterface;
use App\Rolling\Value\Administration\RollingAclHealthDescriptor;
use App\Rolling\Value\Administration\RollingAclHealthReport;

/**
 * Builds a safe health report for Rolling ACL administration integration.
 */
final readonly class RollingAclHealthReportProvider implements RollingAclHealthReportProviderInterface
{
    public function __construct(
        private RollingAclStorageReadinessReportProviderInterface $storageReadinessProvider,
        private RollingAclCapabilityMatrixProviderInterface $capabilityMatrixProvider,
        private RollingAclAdministrationContractMatrixProviderInterface $contractMatrixProvider,
    ) {
    }

    public function report(): RollingAclHealthReport
    {
        $storage = $this->storageReadinessProvider->report();
        $storageMode = $storage->storageMode();
        $doctrineBacked = $storage->doctrineBacked();
        $capabilitySummary = $this->capabilityMatrixProvider->matrix()->toSafeArray()['summary'] ?? [];
        $contractSummary = $this->contractMatrixProvider->matrix()->toSafeArray()['summary'] ?? [];

        return new RollingAclHealthReport(
            new \DateTimeImmutable(),
            [
                new RollingAclHealthDescriptor(
                    'rolling.authorization_owner',
                    'Rolling remains authorization and ACL owner',
                    'ownership',
                    'healthy',
                    'info',
                    false,
                    [
                        'owner' => 'Rolling',
                        'administeringRole' => 'requester_or_visualizer',
                    ],
                ),
                new RollingAclHealthDescriptor(
                    'rolling.acl_storage',
                    'Doctrine-backed ACL storage readiness',
                    'storage',
                    $doctrineBacked ? 'healthy' : 'degraded',
                    $doctrineBacked ? 'info' : 'warning',
                    !$doctrineBacked,
                    [
                        'storageMode' => $storageMode,
                        'expectedEntities' => $storage->expectedEntities(),
                        'pendingCapabilities' => $storage->pendingCapabilities(),
                    ],
                ),
                new RollingAclHealthDescriptor(
                    'rolling.capability_matrix',
                    'Safe ACL capability matrix',
                    'capability',
                    'healthy',
                    'info',
                    false,
                    is_array($capabilitySummary) ? $capabilitySummary : [],
                ),
                new RollingAclHealthDescriptor(
                    'rolling.contract_matrix',
                    'Safe ACL contract matrix',
                    'contract',
                    'healthy',
                    'info',
                    false,
                    is_array($contractSummary) ? $contractSummary : [],
                ),
                new RollingAclHealthDescriptor(
                    'rolling.raw_acl_boundary',
                    'Forbidden raw ACL/security internals boundary',
                    'security_boundary',
                    'protected',
                    'info',
                    false,
                    [
                        'forbiddenPayloads' => ['raw subject grants', 'raw policy internals', 'sessions', 'passwords', 'secrets'],
                    ],
                ),
            ],
            [
                'Rolling health descriptors never expose raw subject grants, raw policy internals, secrets, sessions, or passwords.',
                'A degraded storage check means ACL mutations remain blocked until Doctrine-backed storage is ready.',
            ],
        );
    }
}
