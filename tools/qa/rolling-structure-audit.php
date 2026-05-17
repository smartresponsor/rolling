<?php

declare(strict_types=1);

$root = dirname(__DIR__, 2);
$src = $root.'/src';

$files = [];
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($src, FilesystemIterator::SKIP_DOTS));
foreach ($iterator as $file) {
    if (!$file instanceof SplFileInfo || 'php' !== $file->getExtension()) {
        continue;
    }
    $path = str_replace('\\', '/', substr($file->getPathname(), strlen($root) + 1));
    $code = (string) file_get_contents($file->getPathname());
    preg_match('/^namespace\s+([^;]+);/m', $code, $namespaceMatch);
    preg_match('/^(?:final\s+|abstract\s+)?(?:readonly\s+)?(?:class|interface|trait|enum)\s+(\w+)/m', $code, $classMatch);

    $namespace = $namespaceMatch[1] ?? '';
    $symbol = $classMatch[1] ?? '';
    $expected = 'App\\Rolling\\'.str_replace('/', '\\', substr($path, 4, -4));
    $actual = '' !== $namespace && '' !== $symbol ? $namespace.'\\'.$symbol : '';

    $files[] = [
        'path' => $path,
        'namespace' => $namespace,
        'symbol' => $symbol,
        'expected' => $expected,
        'actual' => $actual,
        'psr4' => '' !== $actual && $actual === $expected,
    ];
}

$byLayer = [];
$psr4Defects = [];
$controllerDefects = [];
foreach ($files as $file) {
    $parts = explode('/', $file['path']);
    $layer = $parts[1] ?? '(root)';
    $byLayer[$layer] = ($byLayer[$layer] ?? 0) + 1;

    if (!$file['psr4']) {
        $psr4Defects[] = $file;
    }

    if (str_starts_with($file['path'], 'src/Controller/') && '' !== $file['symbol'] && !str_ends_with($file['symbol'], 'Controller')) {
        $controllerDefects[] = $file;
    }
}
ksort($byLayer);

$forbiddenRootSdkEntrypoints = [];
foreach (['client.ts', 'index.ts', 'index.ts.example'] as $rootSdkEntrypoint) {
    if (is_file($root.'/'.$rootSdkEntrypoint)) {
        $forbiddenRootSdkEntrypoints[] = $rootSdkEntrypoint;
    }
}



$legacyContextServiceDefects = [];
foreach ([
    'src/Service/Context/EnvContext.php',
    'src/Service/Context/HeaderContext.php',
] as $legacyContextService) {
    if (is_file($root.'/'.$legacyContextService)) {
        $legacyContextServiceDefects[] = $legacyContextService;
    }
}


$legacyApprovalServiceDefects = [];
foreach ([
    'src/Service/Approval/ApprovalGate.php',
] as $legacyApprovalService) {
    if (is_file($root.'/'.$legacyApprovalService)) {
        $legacyApprovalServiceDefects[] = $legacyApprovalService;
    }
}

$legacyAttributeServiceDefects = [];
foreach ([
    'src/Service/Attribute/AttributeService.php',
    'src/Service/Attribute/Cache/ArrayCache.php',
    'src/Service/Attribute/Provider/OrgProvider.php',
    'src/Service/Attribute/Provider/ResourceProvider.php',
    'src/Service/Attribute/Provider/UserProvider.php',
] as $legacyAttributeService) {
    if (is_file($root.'/'.$legacyAttributeService)) {
        $legacyAttributeServiceDefects[] = $legacyAttributeService;
    }
}

$legacyAdminServiceDefects = [];
foreach ([
    'src/Service/Admin/ApprovalWorkflow.php',
    'src/Service/Admin/AdminWorkflowService.php',
] as $legacyAdminService) {
    if (is_file($root.'/'.$legacyAdminService)) {
        $legacyAdminServiceDefects[] = $legacyAdminService;
    }
}

$legacyAdminDtoDefects = [];
foreach ([
    'src/Service/Admin/Dto/ApprovalRequest.php',
] as $legacyAdminDto) {
    if (is_file($root.'/'.$legacyAdminDto)) {
        $legacyAdminDtoDefects[] = $legacyAdminDto;
    }
}

$legacyConsistencyServiceDefects = [];
if (is_file($root.'/src/Service/Consistency/Composer.php')) {
    $legacyConsistencyServiceDefects[] = 'src/Service/Consistency/Composer.php';
}
$legacyConsistencyTokenServiceDefects = [];
foreach ([
    'src/Service/Consistency/Policy/Token.php',
    'src/Service/Consistency/Rebac/Token.php',
] as $legacyConsistencyTokenService) {
    if (is_file($root.'/'.$legacyConsistencyTokenService)) {
        $legacyConsistencyTokenServiceDefects[] = $legacyConsistencyTokenService;
    }
}

$legacyConsistencyTokenSetServiceDefects = [];
foreach ([
    'src/Service/Consistency/TokenSet.php',
] as $legacyConsistencyTokenSetService) {
    if (is_file($root.'/'.$legacyConsistencyTokenSetService)) {
        $legacyConsistencyTokenSetServiceDefects[] = $legacyConsistencyTokenSetService;
    }
}


$legacyCacheServiceDefects = [];
foreach ([
    'src/Service/Cache/Cache.php',
    'src/Service/Cache/Invalidation.php',
    'src/Service/Cache/Partitioner.php',
] as $legacyCacheService) {
    if (is_file($root.'/'.$legacyCacheService)) {
        $legacyCacheServiceDefects[] = $legacyCacheService;
    }
}

$legacyAuditServiceDefects = [];
foreach ([
    'src/Service/Audit/Logger.php',
    'src/Service/Audit/Redactor.php',
] as $legacyAuditService) {
    if (is_file($root.'/'.$legacyAuditService)) {
        $legacyAuditServiceDefects[] = $legacyAuditService;
    }
}


$legacyAuditDtoDefects = [];
foreach ([
    'src/Service/Audit/Dto/DecisionInput.php',
    'src/Service/Audit/Dto/DecisionResult.php',
    'src/Service/Audit/Dto/DecisionRecord.php',
    'src/Service/Audit/Dto/ExplainNode.php',
] as $legacyAuditDto) {
    if (is_file($root.'/'.$legacyAuditDto)) {
        $legacyAuditDtoDefects[] = $legacyAuditDto;
    }
}

$legacyRebacServiceDefects = [];
foreach ([
    'src/Service/Rebac/Checker.php',
    'src/Service/Rebac/Writer.php',
] as $legacyRebacService) {
    if (is_file($root.'/'.$legacyRebacService)) {
        $legacyRebacServiceDefects[] = $legacyRebacService;
    }
}
$legacyModelServiceDefects = [];
foreach ([
    'src/Service/Model/Diff.php',
    'src/Service/Model/Validation.php',
    'src/Service/Model/Migrator.php',
    'src/Service/Model/SchemaRegistry.php',
] as $legacyModelService) {
    if (is_file($root.'/'.$legacyModelService)) {
        $legacyModelServiceDefects[] = $legacyModelService;
    }
}

$legacyExplainServiceDefects = [];
foreach ([
    'src/Service/Explain/TupleReader.php',
    'src/Service/Explain/Planner.php',
    'src/Service/Explain/Renderer.php',
] as $legacyExplainService) {
    if (is_file($root.'/' . $legacyExplainService)) {
        $legacyExplainServiceDefects[] = $legacyExplainService;
    }
}

$legacyPermissionCatalogServiceDefects = [];
foreach ([
    'src/Service/Permission/Catalog/Catalog.php',
    'src/Service/Permission/Catalog/CatalogService.php',
    'src/Service/Permission/Catalog/ConfigLoader.php',
    'src/Service/Permission/Catalog/Hasher.php',
] as $legacyPermissionCatalogService) {
    if (is_file($root.'/'.$legacyPermissionCatalogService)) {
        $legacyPermissionCatalogServiceDefects[] = $legacyPermissionCatalogService;
    }
}


$legacyPipelineServiceDefects = [];
foreach ([
    'src/Service/Pipeline/Decision.php',
    'src/Service/Pipeline/RequestContext.php',
    'src/Service/Pipeline/Trace.php',
] as $legacyPipelineService) {
    if (is_file($root.'/'.$legacyPipelineService)) {
        $legacyPipelineServiceDefects[] = $legacyPipelineService;
    }
}

$legacyPolicyServiceDefects = [];
foreach ([
    'src/Service/Policy/Decision.php',
] as $legacyPolicyService) {
    if (is_file($root.'/'.$legacyPolicyService)) {
        $legacyPolicyServiceDefects[] = $legacyPolicyService;
    }
}

$legacyPelServiceDefects = [];
foreach ([
    'src/Service/Pel/PelEval.php',
    'src/Service/Policy/PelCompiler.php',
] as $legacyPelService) {
    if (is_file($root.'/'.$legacyPelService)) {
        $legacyPelServiceDefects[] = $legacyPelService;
    }
}

$legacyPdpPolicyServiceDefects = [];
foreach ([
    'src/Service/Pdp/Policy/TupleMapper.php',
] as $legacyPdpPolicyService) {
    if (is_file($root.'/' . $legacyPdpPolicyService)) {
        $legacyPdpPolicyServiceDefects[] = $legacyPdpPolicyService;
    }
}

$legacyPdpBatchServiceDefects = [];
foreach ([
    'src/Service/Pdp/BatchDecision.php',
] as $legacyPdpBatchService) {
    if (is_file($root . '/' . $legacyPdpBatchService)) {
        $legacyPdpBatchServiceDefects[] = $legacyPdpBatchService;
    }
}

$legacyPdpDtoDefects = [];
foreach ([
    'src/Service/Pdp/Dto/DecisionRequest.php',
    'src/Service/Pdp/Dto/DecisionResponse.php',
] as $legacyPdpDto) {
    if (is_file($root.'/'.$legacyPdpDto)) {
        $legacyPdpDtoDefects[] = $legacyPdpDto;
    }
}
$legacyCacheSupportServiceDefects = [];
foreach ([
    'src/Service/Cache/StampedeGuard.php',
    'src/Service/Cache/SubjectEpochs.php',
    'src/Service/Cache/TagInvalidator.php',
] as $legacyCacheSupportService) {
    if (is_file($root.'/'.$legacyCacheSupportService)) {
        $legacyCacheSupportServiceDefects[] = $legacyCacheSupportService;
    }
}

$legacyInfrastructureHttpServiceDefects = [];
foreach ([
    'src/Infrastructure/Http/Client.php',
] as $legacyInfrastructureHttpService) {
    if (is_file($root.'/' . $legacyInfrastructureHttpService)) {
        $legacyInfrastructureHttpServiceDefects[] = $legacyInfrastructureHttpService;
    }
}

$legacyRebacHttpInfrastructureServiceDefects = [];
foreach ([
    'src/Infrastructure/Rebac/HttpClient.php',
] as $legacyRebacHttpInfrastructureService) {
    if (is_file($root.'/' . $legacyRebacHttpInfrastructureService)) {
        $legacyRebacHttpInfrastructureServiceDefects[] = $legacyRebacHttpInfrastructureService;
    }
}

$legacyPdpCacheServiceDefects = [];
foreach ([
    'src/Service/Cache/PdpCache.php',
    'src/Service/Pdp/Cache/PdpCache.php',
] as $legacyPdpCacheService) {
    if (is_file($root.'/'.$legacyPdpCacheService)) {
        $legacyPdpCacheServiceDefects[] = $legacyPdpCacheService;
    }
}

$legacySodServiceDefects = [];
foreach ([
    'src/Service/Sod/SodGuard.php',
] as $legacySodService) {
    if (is_file($root.'/'.$legacySodService)) {
        $legacySodServiceDefects[] = $legacySodService;
    }
}

$legacyResidencyServiceDefects = [];
foreach ([
    'src/Service/Residency/ResidencyGuard.php',
] as $legacyResidencyService) {
    if (is_file($root.'/' . $legacyResidencyService)) {
        $legacyResidencyServiceDefects[] = $legacyResidencyService;
    }
}

$legacyMaskServiceDefects = [];
foreach ([
    'src/Service/Mask/DataMasker.php',
] as $legacyMaskService) {
    if (is_file($root.'/' . $legacyMaskService)) {
        $legacyMaskServiceDefects[] = $legacyMaskService;
    }
}

$legacyTenantServiceDefects = [];
foreach ([
    'src/Service/Tenant/Backup.php',
    'src/Service/Tenant/Restore.php',
    'src/Service/Tenant/Limits.php',
    'src/Service/Tenant/Quota.php',
] as $legacyTenantService) {
    if (is_file($root.'/'.$legacyTenantService)) {
        $legacyTenantServiceDefects[] = $legacyTenantService;
    }
}


$forbiddenRootLegacyNotes = [];
foreach (['DELETE_FILES.txt', 'PATCH_NOTES.txt', 'PATCH_NOTES_ROLLING_STRUCTURAL_AUDIT_W02.md', 'PATCH_NOTES_ROLLING_STRUCTURAL_AUDIT_W03.md'] as $rootLegacyNote) {
    if (is_file($root.'/'.$rootLegacyNote)) {
        $forbiddenRootLegacyNotes[] = $rootLegacyNote;
    }
}

$deployOwnershipDefects = [];
if (!is_file($root.'/deploy/compose/compose.yaml')) {
    $deployOwnershipDefects[] = 'missing deploy/compose/compose.yaml';
}
if (!is_file($root.'/deploy/docker/Dockerfile')) {
    $deployOwnershipDefects[] = 'missing deploy/docker/Dockerfile';
}
if (!is_file($root.'/deploy/docker/entrypoint.sh')) {
    $deployOwnershipDefects[] = 'missing deploy/docker/entrypoint.sh';
}

$report = [
    'component' => 'Rolling',
    'namespace' => 'App\\Rolling',
    'php_files' => count($files),
    'layers' => $byLayer,
    'psr4_defects' => $psr4Defects,
    'controller_suffix_defects' => $controllerDefects,
    'root_sdk_entrypoint_defects' => $forbiddenRootSdkEntrypoints,
    'legacy_admin_service_defects' => $legacyAdminServiceDefects,
    'legacy_admin_dto_defects' => $legacyAdminDtoDefects,
    'legacy_approval_service_defects' => $legacyApprovalServiceDefects,
    'legacy_context_service_defects' => $legacyContextServiceDefects,
    'legacy_attribute_service_defects' => $legacyAttributeServiceDefects,
    'legacy_consistency_service_defects' => $legacyConsistencyServiceDefects,
    'legacy_consistency_token_service_defects' => $legacyConsistencyTokenServiceDefects,
    'legacy_consistency_token_set_service_defects' => $legacyConsistencyTokenSetServiceDefects,
    'legacy_cache_service_defects' => $legacyCacheServiceDefects,
    'legacy_cache_support_service_defects' => $legacyCacheSupportServiceDefects,
    'legacy_audit_service_defects' => $legacyAuditServiceDefects,
    'legacy_audit_dto_defects' => $legacyAuditDtoDefects,
    'legacy_rebac_service_defects' => $legacyRebacServiceDefects,
    'root_legacy_note_defects' => $forbiddenRootLegacyNotes,
    'legacy_model_service_defects' => $legacyModelServiceDefects,
    'legacy_residency_service_defects' => $legacyResidencyServiceDefects,
    'legacy_mask_service_defects' => $legacyMaskServiceDefects,
    'legacy_tenant_service_defects' => $legacyTenantServiceDefects,
    'legacy_explain_service_defects' => $legacyExplainServiceDefects,
    'legacy_permission_catalog_service_defects' => $legacyPermissionCatalogServiceDefects,
    'legacy_pipeline_service_defects' => $legacyPipelineServiceDefects,
    'legacy_policy_service_defects' => $legacyPolicyServiceDefects,
    'legacy_pel_service_defects' => $legacyPelServiceDefects,
    'legacy_pdp_policy_service_defects' => $legacyPdpPolicyServiceDefects,
    'legacy_pdp_batch_service_defects' => $legacyPdpBatchServiceDefects,
    'legacy_pdp_dto_defects' => $legacyPdpDtoDefects,
    'legacy_pdp_cache_service_defects' => $legacyPdpCacheServiceDefects,
    'legacy_infrastructure_http_service_defects' => $legacyInfrastructureHttpServiceDefects,
    'legacy_rebac_http_infrastructure_service_defects' => $legacyRebacHttpInfrastructureServiceDefects,
    'legacy_sod_service_defects' => $legacySodServiceDefects,
    'deploy_ownership_defects' => $deployOwnershipDefects,
];

echo json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES).PHP_EOL;

exit([] === $psr4Defects && [] === $controllerDefects && [] === $forbiddenRootSdkEntrypoints && [] === $legacyAdminServiceDefects && [] === $legacyAdminDtoDefects && [] === $legacyApprovalServiceDefects && [] === $legacyContextServiceDefects && [] === $legacyAttributeServiceDefects && [] === $legacyConsistencyServiceDefects && [] === $legacyConsistencyTokenServiceDefects && [] === $legacyConsistencyTokenSetServiceDefects && [] === $legacyCacheServiceDefects && [] === $legacyCacheSupportServiceDefects && [] === $legacyAuditServiceDefects && [] === $legacyAuditDtoDefects && [] === $legacyRebacServiceDefects && [] === $legacyModelServiceDefects && [] === $legacyResidencyServiceDefects && [] === $legacyMaskServiceDefects && [] === $legacyTenantServiceDefects && [] === $legacyExplainServiceDefects && [] === $legacyPermissionCatalogServiceDefects && [] === $legacyPipelineServiceDefects && [] === $legacyPolicyServiceDefects && [] === $legacyPelServiceDefects && [] === $legacyPdpPolicyServiceDefects && [] === $legacyPdpBatchServiceDefects && [] === $legacyPdpDtoDefects && [] === $legacyPdpCacheServiceDefects && [] === $legacyInfrastructureHttpServiceDefects && [] === $legacyRebacHttpInfrastructureServiceDefects && [] === $legacySodServiceDefects && [] === $forbiddenRootLegacyNotes && [] === $deployOwnershipDefects ? 0 : 1);
