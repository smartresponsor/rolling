<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclCapabilityMatrixProviderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationValidatorInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclStorageReadinessReportProviderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAdministrationPermissionCatalogInterface;
use App\Rolling\Value\Administration\RollingAclCapabilityDescriptor;
use App\Rolling\Value\Administration\RollingAclCapabilityMatrix;
use App\Rolling\Value\Administration\RollingAdministrationPermissionDescriptor;

/**
 * Builds a safe ACL capability matrix for Administering/host diagnostics.
 */
final readonly class RollingAclCapabilityMatrixProvider implements RollingAclCapabilityMatrixProviderInterface
{
    public function __construct(
        private RollingAdministrationPermissionCatalogInterface $permissionCatalog,
        private RollingAclMutationValidatorInterface $mutationValidator,
        private RollingAclStorageReadinessReportProviderInterface $storageReadinessProvider,
    ) {
    }

    public function matrix(): RollingAclCapabilityMatrix
    {
        $storage = $this->storageReadinessProvider->report()->toSafeArray();
        $doctrineBacked = true === ($storage['doctrineBacked'] ?? false);
        $storageMode = (string) ($storage['storageMode'] ?? 'bootstrap');

        $capabilities = [];
        foreach ($this->permissionCatalog->descriptors() as $descriptor) {
            $capabilities[] = $this->permissionCapability($descriptor, $storageMode);
        }

        foreach ($this->mutationValidator->allowedMutationTypes() as $mutationType) {
            $capabilities[] = new RollingAclCapabilityDescriptor(
                'rolling.acl.mutation.'.$mutationType,
                'Apply ACL mutation: '.$mutationType,
                'acl_mutation',
                $doctrineBacked ? 'ready' : 'blocked',
                true,
                true,
                true,
                [
                    'mutationType' => $mutationType,
                    'storageMode' => $storageMode,
                    'owner' => 'Rolling',
                    'administeringRole' => 'reviewer_or_requester',
                ],
            );
        }

        return new RollingAclCapabilityMatrix(
            new \DateTimeImmutable(),
            $capabilities,
            [
                'Rolling owns roles, permissions, ACL rules, policy decisions and mutation execution.',
                'Administering may visualize capability metadata and request reviewed mutations only through Rolling.',
                'No capability descriptor may expose raw subject grants, raw policy internals, passwords, sessions, or secrets.',
            ],
        );
    }

    private function permissionCapability(RollingAdministrationPermissionDescriptor $descriptor, string $storageMode): RollingAclCapabilityDescriptor
    {
        return new RollingAclCapabilityDescriptor(
            'rolling.permission.'.$descriptor->key(),
            $descriptor->label(),
            'permission_catalog',
            'ready',
            $descriptor->sensitive(),
            false,
            $descriptor->sensitive(),
            [
                'permissionKey' => $descriptor->key(),
                'category' => $descriptor->category(),
                'scopes' => $descriptor->scopes(),
                'storageMode' => $storageMode,
                'owner' => 'Rolling',
            ],
        );
    }
}
