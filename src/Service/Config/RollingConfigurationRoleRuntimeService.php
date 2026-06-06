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
 * Rolling-owned configuration tool for role runtime settings.
 *
 * This service declares the variables it owns, but it does not depend on
 * Administering writers, SQLite records, or EasyAdmin mechanics. Administering
 * is responsible for discovery, form building, central writing, and indexing.
 */
final readonly class RollingConfigurationRoleRuntimeService implements ConfigToolServiceInterface, ManagedConfigVariablesProviderInterface, ConfigVariableToolServiceInterface
{
    public function __construct(
        private string $projectDir,
    ) {
    }

    public function descriptor(): ConfigToolDescriptor
    {
        return new ConfigToolDescriptor(
            applicationCode: 'Rolling',
            toolCode: 'rolling.role_runtime',
            label: 'Rolling Role Runtime',
            description: 'Runtime knobs for the Rolling role subsystem.',
            formClass: '',
            serviceClass: self::class,
            requiredPermission: 'administration.config.update',
            editableFields: [],
            sensitiveFields: [],
            readableFiles: ['config/component/runtime.yaml'],
            writableFiles: ['config/component/runtime.yaml'],
            metadata: [
                'section' => 'Configuration',
                'kind' => 'runtime',
                'writer_owner' => 'administering',
            ],
            secretNames: [],
            applyStrategy: 'component_runtime_yaml',
        );
    }

    /** @return iterable<ConfigVariable> */
    public function managedVariables(): iterable
    {
        yield ConfigVariable::yaml('role.enabled', 'config/component/runtime.yaml', ConfigVariableType::BOOL)
            ->withLabel('Role subsystem enabled')
            ->required();
        yield ConfigVariable::yaml('role.policy_namespace', 'config/component/runtime.yaml')
            ->withLabel('Policy namespace')
            ->required();
        yield ConfigVariable::yaml('role.admin_namespace', 'config/component/runtime.yaml')
            ->withLabel('Admin namespace')
            ->required();
        yield ConfigVariable::yaml('role.audit_namespace', 'config/component/runtime.yaml')
            ->withLabel('Audit namespace')
            ->required();
        yield ConfigVariable::yaml('role.ops_dir', 'config/component/runtime.yaml')
            ->withLabel('Operations directory')
            ->required();
        yield ConfigVariable::yaml('role.sdk_namespace', 'config/component/runtime.yaml')
            ->withLabel('SDK namespace')
            ->required();
    }

    /** @return array<string, mixed> */
    public function loadVariableData(): array
    {
        $runtime = $this->runtimeManifest();
        $role = is_array($runtime['role'] ?? null) ? $runtime['role'] : [];

        return [
            'role.enabled' => (bool) ($role['enabled'] ?? true),
            'role.policy_namespace' => (string) ($role['policy_namespace'] ?? 'App\\Rolling\\Policy'),
            'role.admin_namespace' => (string) ($role['admin_namespace'] ?? 'App\\Rolling\\Admin'),
            'role.audit_namespace' => (string) ($role['audit_namespace'] ?? 'App\\Rolling\\Audit'),
            'role.ops_dir' => (string) ($role['ops_dir'] ?? 'ops/rolling'),
            'role.sdk_namespace' => (string) ($role['sdk_namespace'] ?? 'App\\Rolling\\SDK'),
        ];
    }

    /**
     * @param array<string, mixed> $variables
     * @param array<string, mixed> $context
     *
     * @return array{status:string, messages:list<string>, masked_changes:array<string, string>, file_changes:array<int, array<string, mixed>>, secret_changes:array<int, array<string, mixed>>}
     */
    public function saveVariables(array $variables, array $context = []): array
    {
        return [
            'status' => 'pending',
            'messages' => ['Rolling declared a variable-based role runtime configuration review. Administering owns persistence and file writing.'],
            'masked_changes' => $this->runtimePatchFromVariables($variables),
            'file_changes' => [],
            'secret_changes' => [],
        ];
    }

    /**
     * @param array<string, mixed> $variables
     * @param array<string, mixed> $context
     *
     * @return array{status:string, messages:list<string>, masked_changes:array<string, string>, file_changes:array<int, array<string, mixed>>, secret_changes:array<int, array<string, mixed>>}
     */
    public function applyVariables(array $variables, array $context = []): array
    {
        return [
            'status' => 'review_required',
            'messages' => ['Rolling does not write host files directly. Apply variable changes through Administering central writer.'],
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
     * @param array<string, mixed> $variables
     *
     * @return array<string, mixed>
     */
    private function runtimePatchFromVariables(array $variables): array
    {
        return [
            'role' => [
                'enabled' => (bool) ($variables['role.enabled'] ?? true),
                'policy_namespace' => (string) ($variables['role.policy_namespace'] ?? ''),
                'admin_namespace' => (string) ($variables['role.admin_namespace'] ?? ''),
                'audit_namespace' => (string) ($variables['role.audit_namespace'] ?? ''),
                'ops_dir' => (string) ($variables['role.ops_dir'] ?? ''),
                'sdk_namespace' => (string) ($variables['role.sdk_namespace'] ?? ''),
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
