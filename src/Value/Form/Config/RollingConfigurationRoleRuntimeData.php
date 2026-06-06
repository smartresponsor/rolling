<?php

declare(strict_types=1);

namespace App\Rolling\Value\Form\Config;

final class RollingConfigurationRoleRuntimeData
{
    public bool $roleEnabled = true;
    public string $rolePolicyNamespace = 'role';
    public string $roleAdminNamespace = 'role-admin';
    public string $roleAuditNamespace = 'role-audit';
    public string $roleOpsDir = '%kernel.project_dir%/ops';
    public string $roleSdkNamespace = 'Rolling\\SDK\\V2';
}
