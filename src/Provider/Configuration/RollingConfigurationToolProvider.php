<?php

declare(strict_types=1);

namespace App\Rolling\Provider\Configuration;

use App\Configuring\ServiceInterface\Tool\ConfigurationToolProviderInterface;
use App\Configuring\Value\Tool\ConfigurationToolDefinition;
use App\Rolling\Contract\RollingIntegrationContract;
use App\Rolling\Service\Config\RollingConfigurationAdministrationCatalogSyncService;
use App\Rolling\Service\Config\RollingConfigurationRoleHierarchyMutationService;
use App\Rolling\Service\Config\RollingConfigurationRoleHierarchyService;
use App\Rolling\Service\Config\RollingConfigurationRoleRuntimeService;
use Symfony\Component\Yaml\Yaml;

/**
 * Rolling's self-registration with Administering.
 *
 * ┌─────────────────────────────────────────────────────────────────┐
 * │  THIS IS THE CANONICAL PATTERN for all 20+ consumer components. │
 * │                                                                 │
 * │  To add a new component (e.g. Billing):                        │
 * │   1. Copy this file → Billing/src/Provider/Configuration/      │
 * │   2. Create BillingIntegrationContract (readonly DTO)           │
 * │   3. Fill in tools() with the component's own tools             │
 * │   4. Wire in Administering/config/services.yaml (3 lines)       │
 * │   5. Administering and all other components: ZERO changes       │
 * └─────────────────────────────────────────────────────────────────┘
 *
 * Responsibilities of THIS class:
 *  - Declare what tools Rolling exposes to the EasyAdmin surface.
 *  - Expose Rolling's integration contract so Administering does NOT
 *    need to hardcode it in its own component.yaml.
 *
 * Administering's responsibility:
 *  - Consume this class through the neutral 'configuring.configuration_tool_provider' tag.
 *  - Discover tools and materialize them into the SQLite index.
 *  - Know nothing else about Rolling.
 */
final class RollingConfigurationToolProvider implements ConfigurationToolProviderInterface
{
    /**
     * @param string $projectDir Rolling's own project root
     *                           (NOT the host kernel.project_dir)
     */
    public function __construct(
        private readonly string $projectDir,
    ) {
    }

    // ------------------------------------------------------------------
    // ConfigurationToolProviderInterface
    // ------------------------------------------------------------------

    public function componentKey(): string
    {
        return 'Rolling';
    }

    public function componentToken(): string
    {
        return 'rolling';
    }

    /**
     * Every tool Rolling wants to expose in the Administering EasyAdmin.
     *
     * Naming convention (enforced by Administering's convention auditor):
     *   serviceShortName  = {Component}Configuration{ToolSlug}Service
     * Dynamic form fields come from the tool service's managedVariables() contract.
     *
     * @return iterable<ConfigurationToolDefinition>
     */
    public function tools(): iterable
    {
        // Tool 1: Role Runtime knobs
        // Allows operators to adjust Rolling's runtime role subsystem
        // configuration directly from the Administering EasyAdmin UI.
        yield new ConfigurationToolDefinition(
            componentKey: $this->componentKey(),
            componentToken: $this->componentToken(),
            toolSlug: 'RoleRuntime',
            serviceClass: RollingConfigurationRoleRuntimeService::class,
            serviceShortName: 'RollingConfigurationRoleRuntimeService',
            label: 'Rolling Role Runtime',
            executable: true,
        );

        yield new ConfigurationToolDefinition(
            componentKey: $this->componentKey(),
            componentToken: $this->componentToken(),
            toolSlug: 'RoleHierarchy',
            serviceClass: RollingConfigurationRoleHierarchyService::class,
            serviceShortName: 'RollingConfigurationRoleHierarchyService',
            label: 'Rolling Role Hierarchy Policy',
            executable: true,
            primaryRouteName: 'administering_rolling_role_hierarchy',
            primaryRouteLabel: 'Open role hierarchy',
        );

        yield new ConfigurationToolDefinition(
            componentKey: $this->componentKey(),
            componentToken: $this->componentToken(),
            toolSlug: 'RoleHierarchyMutation',
            serviceClass: RollingConfigurationRoleHierarchyMutationService::class,
            serviceShortName: 'RollingConfigurationRoleHierarchyMutationService',
            label: 'Rolling Role Hierarchy Mutation',
            executable: true,
            primaryRouteName: 'administration_config_center_index',
            primaryRouteLabel: 'Review hierarchy mutation',
        );

        yield new ConfigurationToolDefinition(
            componentKey: $this->componentKey(),
            componentToken: $this->componentToken(),
            toolSlug: 'AdministrationCatalogSync',
            serviceClass: RollingConfigurationAdministrationCatalogSyncService::class,
            serviceShortName: 'RollingConfigurationAdministrationCatalogSyncService',
            label: 'Rolling Administration Catalog Sync',
            executable: true,
            primaryRouteName: 'administration_config_center_index',
            primaryRouteLabel: 'Sync administration catalog',
        );

        // Add more tools here as Rolling grows.
        // Each yield is a new EasyAdmin tool row — zero Administering changes.
    }

    // ------------------------------------------------------------------
    // Integration contract (consumed by ComponentIntegrationContractRegistry)
    // ------------------------------------------------------------------

    /**
     * Rolling's typed integration contract.
     *
     * Loaded lazily from Rolling's OWN component.yaml — the source of truth
     * stays in Rolling's repository, not in Administering's.
     */
    public function integrationContract(): RollingIntegrationContract
    {
        return RollingIntegrationContract::fromYaml(
            $this->integrationSection()
        );
    }

    // ------------------------------------------------------------------
    // Private
    // ------------------------------------------------------------------

    /**
     * Returns the 'integrations.rolling' section from Rolling's component.yaml.
     *
     * Rolling reads its OWN config — never Administering's.
     *
     * @return array<string, mixed>
     */
    private function integrationSection(): array
    {
        $path = $this->projectDir.'/config/component/component.yaml';

        if (!is_file($path)) {
            return [];
        }

        $parsed = Yaml::parseFile($path);

        // Rolling's component.yaml keeps integration data under
        // integrations.rolling (matching the existing Administering key).
        $section = $parsed['integrations']['rolling'] ?? null;

        return is_array($section) ? $section : [];
    }
}
