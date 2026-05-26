<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclManifestBuilderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAdministrationPermissionCatalogInterface;
use App\Rolling\Value\Administration\RollingAclManifest;
use App\Rolling\Value\Administration\RollingAclManifestAssignmentSummary;
use App\Rolling\Value\Administration\RollingAclManifestPermission;
use App\Rolling\Value\Administration\RollingAclManifestRole;
use App\Rolling\Value\Administration\RollingAdministrationPermissionDescriptor;

final class RollingAclManifestBuilder implements RollingAclManifestBuilderInterface
{
    public function __construct(private readonly RollingAdministrationPermissionCatalogInterface $permissionCatalog)
    {
    }

    public function build(): RollingAclManifest
    {
        return new RollingAclManifest(
            'rolling-acl-manifest-1',
            new \DateTimeImmutable(),
            array_map(
                static fn (RollingAdministrationPermissionDescriptor $descriptor): RollingAclManifestPermission => new RollingAclManifestPermission(
                    $descriptor->key(),
                    $descriptor->label(),
                    $descriptor->category(),
                    $descriptor->scopes(),
                    $descriptor->sensitive(),
                ),
                $this->permissionCatalog->descriptors(),
            ),
            [
                new RollingAclManifestRole('administration.viewer', 'Administration Viewer', [], true),
                new RollingAclManifestRole('administration.operator', 'Administration Operator', ['administration.viewer'], true),
                new RollingAclManifestRole('administration.security_admin', 'Administration Security Admin', ['administration.operator'], true),
            ],
            new RollingAclManifestAssignmentSummary(0, 0, 0),
        );
    }
}
