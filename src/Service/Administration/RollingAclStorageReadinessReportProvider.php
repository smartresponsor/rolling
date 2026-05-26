<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclStorageReadinessReportProviderInterface;
use App\Rolling\Value\Administration\RollingAclStorageReadinessReport;

/**
 * Reports which Rolling ACL persistence capabilities are ready for Administering.
 */
final readonly class RollingAclStorageReadinessReportProvider implements RollingAclStorageReadinessReportProviderInterface
{
    public function report(): RollingAclStorageReadinessReport
    {
        return new RollingAclStorageReadinessReport(
            'doctrine',
            true,
            [
                'RollingRole',
                'RollingPermission',
                'RollingRolePermission',
                'RollingRoleHierarchy',
                'RollingSubjectRoleAssignment',
                'RollingAclRule',
            ],
            [
                'permission catalog manifest',
                'safe ACL mutation review',
                'safe ACL mutation apply request',
                'execution event contract',
                'Doctrine-backed ACL mutation execution',
                'persisted execution event repository',
                'persisted subject-role assignment mutation',
                'persisted ACL rule mutation',
            ],
            [
                'production migration generation from Doctrine metadata',
            ],
        );
    }
}
