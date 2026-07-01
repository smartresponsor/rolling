<?php

declare(strict_types=1);

namespace App\Rolling\Service\Config;

use App\Configuring\ServiceInterface\Config\ConfigToolServiceInterface;
use App\Configuring\ServiceInterface\Config\ConfigVariableToolServiceInterface;
use App\Configuring\ServiceInterface\Config\ManagedConfigVariablesProviderInterface;
use App\Configuring\Value\Config\ConfigToolDescriptor;
use App\Configuring\Value\Config\ConfigVariable;
use App\Configuring\Value\Config\ConfigVariableType;
use Symfony\Component\Yaml\Yaml;

/**
 * Rolling-owned configuration tool for the default role hierarchy policy.
 *
 * Administering may discover this service and build a generic form from
 * managedVariables(), but Rolling remains the owner of the hierarchy semantics.
 * Direct table editing is intentionally not exposed here; state-changing
 * hierarchy changes must go through a reviewed Rolling-owned mutation flow.
 */
final readonly class RollingConfigurationRoleHierarchyService implements ConfigToolServiceInterface, ManagedConfigVariablesProviderInterface, ConfigVariableToolServiceInterface
{
    public function __construct(
        private string $projectDir,
    ) {
    }

    public function descriptor(): ConfigToolDescriptor
    {
        return new ConfigToolDescriptor(
            applicationCode: 'Rolling',
            toolCode: 'rolling.role_hierarchy',
            label: 'Rolling Role Hierarchy Policy',
            description: 'Default hierarchy bootstrap and reviewed hierarchy-management policy for Rolling roles.',
            formClass: '',
            serviceClass: self::class,
            requiredPermission: 'administration.rolling.acl_mutation.review',
            editableFields: [],
            sensitiveFields: [],
            readableFiles: ['config/component/runtime.yaml'],
            writableFiles: ['config/component/runtime.yaml'],
            metadata: [
                'section' => 'Rolling ACL',
                'kind' => 'role_hierarchy_policy',
                'writer_owner' => 'administering',
                'mutation_owner' => 'rolling',
            ],
            secretNames: [],
            applyStrategy: 'component_runtime_yaml',
        );
    }

    /** @return iterable<ConfigVariable> */
    public function managedVariables(): iterable
    {
        yield ConfigVariable::yaml('role_hierarchy.enabled', 'config/component/runtime.yaml', ConfigVariableType::BOOL)
            ->withLabel('Default role hierarchy enabled')
            ->required();
        yield ConfigVariable::yaml('role_hierarchy.review_required', 'config/component/runtime.yaml', ConfigVariableType::BOOL)
            ->withLabel('Require review for hierarchy changes')
            ->required();
        yield ConfigVariable::yaml('role_hierarchy.bootstrap_viewer_role', 'config/component/runtime.yaml')
            ->withLabel('Bootstrap viewer role')
            ->required();
        yield ConfigVariable::yaml('role_hierarchy.bootstrap_operator_role', 'config/component/runtime.yaml')
            ->withLabel('Bootstrap operator role')
            ->required();
        yield ConfigVariable::yaml('role_hierarchy.bootstrap_security_admin_role', 'config/component/runtime.yaml')
            ->withLabel('Bootstrap security admin role')
            ->required();
        yield ConfigVariable::yaml('role_hierarchy.default_edges', 'config/component/runtime.yaml', ConfigVariableType::LIST)
            ->withLabel('Default hierarchy edges')
            ->withMetadata([
                'item_shape' => 'parent>child',
                'example' => ['administration.operator>administration.viewer'],
            ]);
    }

    /** @return array<string, mixed> */
    public function loadVariableData(): array
    {
        $runtime = $this->runtimeManifest();
        $hierarchy = is_array($runtime['role_hierarchy'] ?? null) ? $runtime['role_hierarchy'] : [];

        return [
            'role_hierarchy.enabled' => (bool) ($hierarchy['enabled'] ?? true),
            'role_hierarchy.review_required' => (bool) ($hierarchy['review_required'] ?? true),
            'role_hierarchy.bootstrap_viewer_role' => (string) ($hierarchy['bootstrap_viewer_role'] ?? 'administration.viewer'),
            'role_hierarchy.bootstrap_operator_role' => (string) ($hierarchy['bootstrap_operator_role'] ?? 'administration.operator'),
            'role_hierarchy.bootstrap_security_admin_role' => (string) ($hierarchy['bootstrap_security_admin_role'] ?? 'administration.security_admin'),
            'role_hierarchy.default_edges' => $this->defaultEdges($hierarchy['default_edges'] ?? null),
        ];
    }

    /**
     * @param array<string, mixed> $variables
     * @param array<string, mixed> $context
     *
     * @return array{status:string, messages:list<string>, masked_changes:array<string, mixed>, file_changes:array<int, array<string, mixed>>, secret_changes:array<int, array<string, mixed>>}
     */
    public function saveVariables(array $variables, array $context = []): array
    {
        return [
            'status' => 'pending',
            'messages' => ['Rolling declared a reviewed role hierarchy policy change. Administering owns persistence and file writing.'],
            'masked_changes' => $this->runtimePatchFromVariables($variables),
            'file_changes' => [],
            'secret_changes' => [],
        ];
    }

    /**
     * @param array<string, mixed> $variables
     * @param array<string, mixed> $context
     *
     * @return array{status:string, messages:list<string>, masked_changes:array<string, mixed>, file_changes:array<int, array<string, mixed>>, secret_changes:array<int, array<string, mixed>>}
     */
    public function applyVariables(array $variables, array $context = []): array
    {
        return [
            'status' => 'review_required',
            'messages' => ['Rolling role hierarchy changes must be reviewed before apply. Use the Rolling ACL mutation flow for state-changing hierarchy edits.'],
            'masked_changes' => $this->runtimePatchFromVariables($variables),
            'file_changes' => [],
            'secret_changes' => [],
        ];
    }

    /** @return array<string, mixed> */
    private function runtimeManifest(): array
    {
        $path = $this->projectDir.'/../Rolling/config/component/runtime.yaml';
        $parsed = is_file($path) ? Yaml::parseFile($path) : [];

        return is_array($parsed) ? $parsed : [];
    }

    /**
     * @return list<string>
     */
    private function defaultEdges(mixed $edges): array
    {
        if (!is_array($edges)) {
            return [
                'administration.operator>administration.viewer',
                'administration.security_admin>administration.operator',
            ];
        }

        $normalized = [];
        foreach ($edges as $edge) {
            if (is_string($edge) && '' !== trim($edge)) {
                $normalized[] = trim($edge);
            }
        }

        return [] === $normalized ? [
            'administration.operator>administration.viewer',
            'administration.security_admin>administration.operator',
        ] : $normalized;
    }

    /**
     * @param array<string, mixed> $variables
     *
     * @return array<string, mixed>
     */
    private function runtimePatchFromVariables(array $variables): array
    {
        $edges = $variables['role_hierarchy.default_edges'] ?? [];
        if (is_string($edges)) {
            $edges = array_values(array_filter(array_map('trim', preg_split('/\r?\n/', $edges) ?: [])));
        }

        return [
            'role_hierarchy' => [
                'enabled' => (bool) ($variables['role_hierarchy.enabled'] ?? true),
                'review_required' => (bool) ($variables['role_hierarchy.review_required'] ?? true),
                'bootstrap_viewer_role' => (string) ($variables['role_hierarchy.bootstrap_viewer_role'] ?? 'administration.viewer'),
                'bootstrap_operator_role' => (string) ($variables['role_hierarchy.bootstrap_operator_role'] ?? 'administration.operator'),
                'bootstrap_security_admin_role' => (string) ($variables['role_hierarchy.bootstrap_security_admin_role'] ?? 'administration.security_admin'),
                'default_edges' => is_array($edges) ? array_values($edges) : [],
            ],
        ];
    }

    public function loadData(): object
    {
        return new \ArrayObject($this->loadVariableData());
    }

    public function save(object $data, array $context = []): array
    {
        return $this->saveVariables($this->objectToArray($data), $context);
    }

    public function apply(object $data, array $context = []): array
    {
        return $this->applyVariables($this->objectToArray($data), $context);
    }

    /** @return array<string, mixed> */
    private function objectToArray(object $data): array
    {
        return $data instanceof \Traversable ? iterator_to_array($data) : get_object_vars($data);
    }
}
