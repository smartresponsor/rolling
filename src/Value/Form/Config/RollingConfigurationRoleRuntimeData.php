<?php

declare(strict_types=1);

namespace App\Rolling\Value\Form\Config;

final class RollingConfigurationRoleRuntimeData
{
    public bool $enabled = true;
    public string $policyNamespace = 'role';
    public string $adminNamespace = 'role-admin';
    public string $auditNamespace = 'role-audit';
    public string $opsDir = '%kernel.project_dir%/ops';
    public string $sdkNamespace = 'Rolling\\SDK\\V2';
}
