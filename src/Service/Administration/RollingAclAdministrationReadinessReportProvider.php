<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclAdministrationReadinessReportProviderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclManifestBuilderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationExecutionReportProviderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationValidatorInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclStorageReadinessReportProviderInterface;
use App\Rolling\Value\Administration\RollingAclAdministrationReadinessReport;
use App\Rolling\Value\Administration\RollingAclMutationExecutionFilter;

/**
 * Builds a safe readiness summary for Rolling ACL administration integration.
 */
final readonly class RollingAclAdministrationReadinessReportProvider implements RollingAclAdministrationReadinessReportProviderInterface
{
    public function __construct(
        private RollingAclManifestBuilderInterface $manifestBuilder,
        private RollingAclMutationValidatorInterface $validator,
        private RollingAclMutationExecutionReportProviderInterface $executionReportProvider,
        private RollingAclStorageReadinessReportProviderInterface $storageReadinessProvider,
    ) {
    }

    public function report(): RollingAclAdministrationReadinessReport
    {
        $manifest = $this->manifestBuilder->build();
        $executionReport = $this->executionReportProvider->report(new RollingAclMutationExecutionFilter(limit: 25));
        $storageReadiness = $this->storageReadinessProvider->report();

        return new RollingAclAdministrationReadinessReport(
            new \DateTimeImmutable(),
            $this->validator->allowedMutationTypes(),
            [
                'permissionCount' => count($manifest->permissions()),
                'roleCount' => count($manifest->roles()),
                'assignmentSummary' => null === $manifest->assignmentSummary() ? null : $manifest->assignmentSummary()->toArray(),
                'version' => $manifest->version(),
            ],
            $executionReport->summary()->toSafeArray(),
            $storageReadiness->toSafeArray(),
        );
    }
}
