<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAdministrationPermissionCatalogInterface;
use App\Rolling\Value\Administration\RollingAdministrationPermissionDescriptor;
use App\Rolling\Value\Administration\RollingManagingFieldPermissionVocabulary;

final class RollingAdministrationPermissionCatalog implements RollingAdministrationPermissionCatalogInterface
{
    /** @return list<string> */
    public function permissions(): array
    {
        return array_map(
            static fn (RollingAdministrationPermissionDescriptor $descriptor): string => $descriptor->key(),
            $this->descriptors(),
        );
    }

    /** @return list<RollingAdministrationPermissionDescriptor> */
    public function descriptors(): array
    {
        return [
            ...RollingManagingFieldPermissionVocabulary::descriptors(),
            new RollingAdministrationPermissionDescriptor('administration.dashboard.view', 'View Admin dashboard', 'administration', ['global']),
            new RollingAdministrationPermissionDescriptor('administration.operation.view', 'View Administering operation runs and reports', 'administration_operations', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('administration.configuration.scan', 'Queue Administering configuration scan operation', 'administration_operations', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('administration.credential.presence_check', 'Queue credential presence check operation', 'administration_operations', ['environment', 'component'], true),
            new RollingAdministrationPermissionDescriptor('administration.composer.validate', 'Queue Composer validation operation', 'administration_operations', ['component']),
            new RollingAdministrationPermissionDescriptor('administration.rolling_acl.catalog_refresh', 'Queue Rolling ACL catalog refresh operation', 'administration_operations', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('administration.accessing_account.action', 'Queue Accessing account action operation', 'administration_operations', ['global']),
            new RollingAdministrationPermissionDescriptor('administration.symfony_secret.set', 'Set credential through Symfony Secrets', 'credentials', ['environment', 'component'], true),
            new RollingAdministrationPermissionDescriptor('administration.symfony_secret.remove', 'Remove credential through Symfony Secrets', 'credentials', ['environment', 'component'], true),
            new RollingAdministrationPermissionDescriptor('administration.generated_patch.build', 'Build generated patch artifact', 'delivery', ['application', 'component'], true),

            new RollingAdministrationPermissionDescriptor('administration.accessing.account.view', 'View Accessing accounts through Administering', 'accessing_accounts', ['global']),
            new RollingAdministrationPermissionDescriptor('administration.accessing.account_action.view', 'View Accessing account action surface', 'accessing_accounts', ['global']),
            new RollingAdministrationPermissionDescriptor('administration.accessing.account_action.execute', 'Execute safe Accessing account action request', 'accessing_accounts', ['global'], true),
            new RollingAdministrationPermissionDescriptor('administration.accessing.account_action.audit.view', 'View Accessing account action audit projections', 'accessing_accounts', ['global']),

            new RollingAdministrationPermissionDescriptor('administration.rolling.permission_catalog.view', 'View Rolling permission catalog through Administering', 'rolling_acl', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('administration.rolling.subject_access_report.view', 'View Rolling subject access report through Administering', 'rolling_acl', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('administration.rolling.acl_mutation.review.view', 'View Rolling ACL mutation review surface', 'rolling_acl', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('administration.rolling.acl_mutation.review', 'Create Rolling ACL mutation review records', 'rolling_acl', ['global', 'component'], true),
            new RollingAdministrationPermissionDescriptor('administration.rolling.acl_mutation.apply', 'Apply approved Rolling ACL mutations', 'rolling_acl', ['global', 'component'], true),
            new RollingAdministrationPermissionDescriptor('administration.rolling.acl_mutation.apply.view', 'View Rolling ACL mutation apply reports', 'rolling_acl', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('administration.rolling.acl.execution_report.view', 'View Rolling ACL execution reports', 'rolling_acl', ['global', 'component']),

            new RollingAdministrationPermissionDescriptor('administration.connected_component.overview.view', 'View connected component overview', 'connected_components', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('administration.connected_component.health.view', 'View connected component health report', 'connected_components', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('administration.connected_component.readiness.view', 'View connected component readiness report', 'connected_components', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('administration.connected_component.capability_matrix.view', 'View connected component capability matrix', 'connected_components', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('administration.connected_component.contract_matrix.view', 'View connected component contract matrix', 'connected_components', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('administration.connected_component.diagnostics.view', 'View connected component diagnostics', 'connected_components', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('administration.connected_component.execution_plan.view', 'View connected component execution plan', 'connected_components', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('administration.connected_component.remediation.view', 'View connected component remediation plan', 'connected_components', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('administration.connected_component.work_plan.view', 'View connected component work plan', 'connected_components', ['global', 'component']),

            new RollingAdministrationPermissionDescriptor('administration.config.view', 'View configuration state', 'configuration', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('administration.config.update', 'Propose configuration changes', 'configuration', ['component'], true),
            new RollingAdministrationPermissionDescriptor('administration.secret.set', 'Set credential through Symfony Secrets', 'credentials', ['environment', 'component'], true),
            new RollingAdministrationPermissionDescriptor('administration.secret.rotate', 'Rotate credential through Symfony Secrets', 'credentials', ['environment', 'component'], true),
            new RollingAdministrationPermissionDescriptor('administration.composer.propose', 'Propose Composer changes', 'configuration', ['application'], true),
            new RollingAdministrationPermissionDescriptor('administration.patch.generate', 'Generate reviewed patch artifacts', 'delivery', ['application', 'component']),
            new RollingAdministrationPermissionDescriptor('rolling.role.view', 'View Rolling roles', 'rolling_acl', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('rolling.role.update', 'Update Rolling roles', 'rolling_acl', ['global', 'component'], true),
            new RollingAdministrationPermissionDescriptor('rolling.permission.view', 'View Rolling permissions', 'rolling_acl', ['global', 'component']),
            new RollingAdministrationPermissionDescriptor('rolling.permission.update', 'Update Rolling permissions', 'rolling_acl', ['global', 'component'], true),
            new RollingAdministrationPermissionDescriptor('accessing.account.view', 'View Accessing accounts', 'accessing_accounts', ['global']),
            new RollingAdministrationPermissionDescriptor('accessing.account.lock', 'Lock or unlock Accessing accounts', 'accessing_accounts', ['global'], true),
            new RollingAdministrationPermissionDescriptor('accessing.session.terminate', 'Terminate Accessing sessions', 'accessing_accounts', ['global'], true),
        ];
    }
}
