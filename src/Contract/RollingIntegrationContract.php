<?php

declare(strict_types=1);

namespace App\Rolling\Contract;

/**
 * Rolling's integration contract — the single source of truth for what
 * Rolling exposes to Administering and any other consumer.
 *
 * Rules:
 *  - Rolling owns this file.
 *  - Administering must NOT duplicate these values in its component.yaml.
 *  - Consumers call ComponentIntegrationContractRegistry::get('rolling')
 *    and read only the fields they need.
 *  - All class references are strings (no compile-time coupling to neighbors).
 */
final readonly class RollingIntegrationContract
{
    // -----------------------------------------------------------------
    // Ecosystem identity
    // -----------------------------------------------------------------

    /** What Rolling owns in the ecosystem */
    public string $owns;

    // -----------------------------------------------------------------
    // ACL decision services (consumed by Administering permission checker)
    // -----------------------------------------------------------------

    /** FQCN of the Rolling ACL permission decision service */
    public string $decisionContract;

    /** FQCN of the Rolling field-access decision service */
    public string $fieldDecisionContract;

    // -----------------------------------------------------------------
    // Managing / Rolling field-access adapter
    // -----------------------------------------------------------------

    /** Managing external access backend identifier: 'rolling' | 'none' */
    public string $managingExternalAccessBackend;

    /** Effect when Rolling call fails: 'deny' | 'allow' */
    public string $managingExternalAccessFailureEffect;

    /** Permission key Rolling uses for managing field-value access */
    public string $managingExternalAccessPermissionKey;

    /** FQCN of the concrete Managing → Rolling resolver adapter */
    public string $managingExternalAccessAdapter;

    /** FQCN of the external access resolver contract (Managing side) */
    public string $managingExternalAccessResolverContract;

    /** Route nameEntity of the Rolling-backed Managing access readiness surface */
    public string $managingExternalAccessReadinessSurface;

    // -----------------------------------------------------------------
    // Managing field visibility
    // -----------------------------------------------------------------

    /** FQCN of the Managing field visibility explanation resolver */
    public string $managingVisibilityExplanationContract;

    /** Decision axes used in explanations: ['access', 'presentation', 'availability'] */
    public array $managingVisibilityExplanationAxes;

    /** FQCN of the Managing field visibility inspector */
    public string $managingVisibilityInspectionContract;

    // -----------------------------------------------------------------
    // Managing field-access policy
    // -----------------------------------------------------------------

    /** FQCN of the Managing field access policy descriptor validator */
    public string $managingFieldAccessDescriptorValidator;

    /** Path to the field access catalog hardening architecture doc */
    public string $managingFieldAccessHardeningDoc;

    /** Path to the Rolling/Managing/Administering field visibility readiness doc */
    public string $managingFieldVisibilityReadinessDoc;

    // -----------------------------------------------------------------
    // Managing field view profiles
    // -----------------------------------------------------------------

    /** FQCN of the Managing field view profile apply handler */
    public string $managingProfileApplyContract;

    /** FQCN of the Managing field view profile storage entity */
    public string $managingProfileStorageEntity;

    /** Entity manager nameEntity for profile storage ('system') */
    public string $managingProfileStorageEntityManager;

    // -----------------------------------------------------------------
    // Administering route names (owned by Rolling semantically,
    // registered in Administering's router)
    // -----------------------------------------------------------------

    public string $fieldAccessApplySurface;
    public string $fieldViewProfileSurface;
    public string $fieldViewProfilePrioritySurface;
    public string $fieldViewProfileReviewSurface;
    public string $fieldViewProfileApplySurface;
    public string $fieldVisibilityExplanationSurface;
    public string $fieldVisibilityInspectionSurface;

    // -----------------------------------------------------------------
    // Service section anchor sync operation
    // -----------------------------------------------------------------

    public string $serviceSectionAnchorSyncOperation;
    public string $serviceSectionAnchorSyncDoc;

    public function __construct(
        string $owns,
        string $decisionContract,
        string $fieldDecisionContract,
        string $managingExternalAccessBackend,
        string $managingExternalAccessFailureEffect,
        string $managingExternalAccessPermissionKey,
        string $managingExternalAccessAdapter,
        string $managingExternalAccessResolverContract,
        string $managingExternalAccessReadinessSurface,
        string $managingVisibilityExplanationContract,
        array $managingVisibilityExplanationAxes,
        string $managingVisibilityInspectionContract,
        string $managingFieldAccessDescriptorValidator,
        string $managingFieldAccessHardeningDoc,
        string $managingFieldVisibilityReadinessDoc,
        string $managingProfileApplyContract,
        string $managingProfileStorageEntity,
        string $managingProfileStorageEntityManager,
        string $fieldAccessApplySurface,
        string $fieldViewProfileSurface,
        string $fieldViewProfilePrioritySurface,
        string $fieldViewProfileReviewSurface,
        string $fieldViewProfileApplySurface,
        string $fieldVisibilityExplanationSurface,
        string $fieldVisibilityInspectionSurface,
        string $serviceSectionAnchorSyncOperation,
        string $serviceSectionAnchorSyncDoc,
    ) {
        $this->owns = $owns;
        $this->decisionContract = $decisionContract;
        $this->fieldDecisionContract = $fieldDecisionContract;
        $this->managingExternalAccessBackend = $managingExternalAccessBackend;
        $this->managingExternalAccessFailureEffect = $managingExternalAccessFailureEffect;
        $this->managingExternalAccessPermissionKey = $managingExternalAccessPermissionKey;
        $this->managingExternalAccessAdapter = $managingExternalAccessAdapter;
        $this->managingExternalAccessResolverContract = $managingExternalAccessResolverContract;
        $this->managingExternalAccessReadinessSurface = $managingExternalAccessReadinessSurface;
        $this->managingVisibilityExplanationContract = $managingVisibilityExplanationContract;
        $this->managingVisibilityExplanationAxes = $managingVisibilityExplanationAxes;
        $this->managingVisibilityInspectionContract = $managingVisibilityInspectionContract;
        $this->managingFieldAccessDescriptorValidator = $managingFieldAccessDescriptorValidator;
        $this->managingFieldAccessHardeningDoc = $managingFieldAccessHardeningDoc;
        $this->managingFieldVisibilityReadinessDoc = $managingFieldVisibilityReadinessDoc;
        $this->managingProfileApplyContract = $managingProfileApplyContract;
        $this->managingProfileStorageEntity = $managingProfileStorageEntity;
        $this->managingProfileStorageEntityManager = $managingProfileStorageEntityManager;
        $this->fieldAccessApplySurface = $fieldAccessApplySurface;
        $this->fieldViewProfileSurface = $fieldViewProfileSurface;
        $this->fieldViewProfilePrioritySurface = $fieldViewProfilePrioritySurface;
        $this->fieldViewProfileReviewSurface = $fieldViewProfileReviewSurface;
        $this->fieldViewProfileApplySurface = $fieldViewProfileApplySurface;
        $this->fieldVisibilityExplanationSurface = $fieldVisibilityExplanationSurface;
        $this->fieldVisibilityInspectionSurface = $fieldVisibilityInspectionSurface;
        $this->serviceSectionAnchorSyncOperation = $serviceSectionAnchorSyncOperation;
        $this->serviceSectionAnchorSyncDoc = $serviceSectionAnchorSyncDoc;
    }

    /**
     * Build from the 'integrations.rolling' section of Rolling's component.yaml.
     *
     * @param array<string, mixed> $data
     */
    public static function fromYaml(array $data): self
    {
        return new self(
            owns: (string) ($data['owns'] ?? 'authorization_acl_policy'),
            decisionContract: (string) ($data['decision_contract'] ?? ''),
            fieldDecisionContract: (string) ($data['field_decision_contract'] ?? ''),
            managingExternalAccessBackend: (string) ($data['managing_rolling_external_access_backend'] ?? 'none'),
            managingExternalAccessFailureEffect: (string) ($data['managing_rolling_external_access_failure_effect'] ?? 'deny'),
            managingExternalAccessPermissionKey: (string) ($data['managing_rolling_external_access_permission_key'] ?? 'managing.field.view'),
            managingExternalAccessAdapter: (string) ($data['managing_rolling_external_access_adapter'] ?? ''),
            managingExternalAccessResolverContract: (string) ($data['managing_external_access_resolver_contract'] ?? ''),
            managingExternalAccessReadinessSurface: (string) ($data['managing_rolling_external_access_readiness_surface'] ?? ''),
            managingVisibilityExplanationContract: (string) ($data['managing_visibility_explanation_contract'] ?? ''),
            managingVisibilityExplanationAxes: (array) ($data['managing_visibility_explanation_axes'] ?? ['access', 'presentation', 'availability']),
            managingVisibilityInspectionContract: (string) ($data['managing_visibility_inspection_contract'] ?? ''),
            managingFieldAccessDescriptorValidator: (string) ($data['managing_field_access_descriptor_validator'] ?? ''),
            managingFieldAccessHardeningDoc: (string) ($data['managing_field_access_hardening_doc'] ?? ''),
            managingFieldVisibilityReadinessDoc: (string) ($data['managing_field_visibility_readiness_doc'] ?? ''),
            managingProfileApplyContract: (string) ($data['managing_profile_apply_contract'] ?? ''),
            managingProfileStorageEntity: (string) ($data['managing_profile_storage_entity'] ?? ''),
            managingProfileStorageEntityManager: (string) ($data['managing_profile_storage_entity_manager'] ?? 'system'),
            fieldAccessApplySurface: (string) ($data['field_access_apply_surface'] ?? ''),
            fieldViewProfileSurface: (string) ($data['field_view_profile_surface'] ?? ''),
            fieldViewProfilePrioritySurface: (string) ($data['field_view_profile_priority_surface'] ?? ''),
            fieldViewProfileReviewSurface: (string) ($data['field_view_profile_review_surface'] ?? ''),
            fieldViewProfileApplySurface: (string) ($data['field_view_profile_apply_surface'] ?? ''),
            fieldVisibilityExplanationSurface: (string) ($data['field_visibility_explanation_surface'] ?? ''),
            fieldVisibilityInspectionSurface: (string) ($data['field_visibility_inspection_surface'] ?? ''),
            serviceSectionAnchorSyncOperation: (string) ($data['service_section_anchor_sync_operation'] ?? ''),
            serviceSectionAnchorSyncDoc: (string) ($data['service_section_anchor_sync_doc'] ?? ''),
        );
    }
}
