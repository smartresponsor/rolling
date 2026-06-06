<?php

declare(strict_types=1);

namespace App\Rolling\Value\Form\Config;

final class RollingConfigurationRoleHierarchyData
{
    public bool $roleHierarchyEnabled = true;
    public bool $roleHierarchyReviewRequired = true;
    public string $roleHierarchyBootstrapViewerRole = 'administration.viewer';
    public string $roleHierarchyBootstrapOperatorRole = 'administration.operator';
    public string $roleHierarchyBootstrapSecurityAdminRole = 'administration.security_admin';

    /** @var list<string> */
    public array $roleHierarchyDefaultEdges = [
        'administration.operator>administration.viewer',
        'administration.security_admin>administration.operator',
    ];
}
