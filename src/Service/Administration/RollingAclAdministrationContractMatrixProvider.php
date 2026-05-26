<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\ServiceInterface\Administration\RollingAclAdministrationContractMatrixProviderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationValidatorInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclStorageReadinessReportProviderInterface;
use App\Rolling\Value\Administration\RollingAclAdministrationContractDescriptor;
use App\Rolling\Value\Administration\RollingAclAdministrationContractMatrix;

/**
 * Builds a safe contract matrix for Rolling ACL administration consumers.
 */
final readonly class RollingAclAdministrationContractMatrixProvider implements RollingAclAdministrationContractMatrixProviderInterface
{
    public function __construct(
        private RollingAclStorageReadinessReportProviderInterface $storageReadinessProvider,
        private RollingAclMutationValidatorInterface $mutationValidator,
    ) {
    }

    public function matrix(): RollingAclAdministrationContractMatrix
    {
        $storage = $this->storageReadinessProvider->report();
        $storageMode = $storage->storageMode();
        $doctrineBacked = $storage->doctrineBacked();

        return new RollingAclAdministrationContractMatrix(
            new \DateTimeImmutable(),
            [
                $this->contract('rolling.permission_catalog', 'Administration permission catalog', 'catalog', 'ready', true, false, $storageMode),
                $this->contract('rolling.acl_manifest_builder', 'Safe ACL manifest builder', 'manifest', 'ready', true, false, $storageMode),
                $this->contract('rolling.acl_manifest_exporter', 'Safe ACL manifest exporter', 'manifest', 'ready', false, false, $storageMode),
                $this->contract('rolling.acl_mutation_validator', 'ACL mutation request validator', 'mutation_review', 'ready', true, true, $storageMode, [
                    'allowedMutationTypes' => $this->mutationValidator->allowedMutationTypes(),
                ]),
                $this->contract('rolling.acl_mutation_review_builder', 'ACL mutation dry-run review builder', 'mutation_review', 'ready', true, true, $storageMode),
                $this->contract('rolling.acl_mutation_apply_request_builder', 'ACL mutation apply request builder', 'mutation_apply', 'ready', true, true, $storageMode),
                $this->contract('rolling.acl_mutation_execution_gateway', 'ACL mutation execution gateway', 'mutation_execution', $doctrineBacked ? 'ready' : 'blocked', true, true, $storageMode),
                $this->contract('rolling.acl_storage_persistence', 'Doctrine-backed ACL persistence', 'storage', $doctrineBacked ? 'ready' : 'blocked', true, true, $storageMode, [
                    'expectedEntities' => $storage->expectedEntities(),
                    'pendingCapabilities' => $storage->pendingCapabilities(),
                ]),
                $this->contract('rolling.raw_grants_exposure', 'Raw subject grants exposure boundary', 'forbidden_boundary', 'forbidden', true, true, $storageMode, [
                    'forbiddenPayloads' => ['raw subject grants', 'raw policy internals', 'sessions', 'passwords', 'secrets'],
                ]),
            ],
            [
                'Rolling owns roles, permissions, ACL rules, policy decisions and mutation execution.',
                'Administering may consume safe contracts and submit reviewed mutation requests only.',
                'Sensitive descriptors describe contract risk only; they do not expose raw grants or policy internals.',
            ],
        );
    }

    /** @param array<string, mixed> $context */
    private function contract(
        string $key,
        string $label,
        string $category,
        string $status,
        bool $required,
        bool $sensitive,
        string $storageMode,
        array $context = [],
    ): RollingAclAdministrationContractDescriptor {
        return new RollingAclAdministrationContractDescriptor(
            $key,
            $label,
            $category,
            $status,
            'Rolling',
            'Administering',
            $required,
            $sensitive,
            $storageMode,
            $context + [
                'owner' => 'Rolling',
                'administeringRole' => 'consumer_or_requester',
            ],
        );
    }
}
