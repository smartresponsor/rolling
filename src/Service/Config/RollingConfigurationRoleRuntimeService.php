<?php

declare(strict_types=1);

namespace App\Rolling\Service\Config;

use App\Administering\Service\Config\ConfigApplyService;
use App\Administering\Service\Config\ConfigFileWriterService;
use App\Administering\ServiceInterface\Config\AdministrationConfigToolServiceInterface;
use App\Administering\Value\Config\AdministrationConfigToolDescriptor;
use App\Rolling\Form\Config\RollingConfigurationRoleRuntimeFormType;
use App\Rolling\Value\Form\Config\RollingConfigurationRoleRuntimeData;
use Symfony\Component\Yaml\Yaml;

final readonly class RollingConfigurationRoleRuntimeService implements AdministrationConfigToolServiceInterface
{
    public function __construct(
        private string $projectDir,
        private ConfigApplyService $applyService,
        private ConfigFileWriterService $fileWriter,
    ) {
    }

    public function descriptor(): AdministrationConfigToolDescriptor
    {
        return new AdministrationConfigToolDescriptor(
            applicationCode: 'Rolling',
            toolCode: 'rolling.role_runtime',
            label: 'Rolling Role Runtime',
            description: 'Runtime knobs for the Rolling role subsystem.',
            formClass: RollingConfigurationRoleRuntimeFormType::class,
            serviceClass: self::class,
            requiredPermission: 'administration.config.update',
            editableFields: ['enabled', 'policyNamespace', 'adminNamespace', 'auditNamespace', 'opsDir', 'sdkNamespace'],
            sensitiveFields: [],
            readableFiles: ['config/component/runtime.yaml'],
            writableFiles: ['config/component/runtime.yaml'],
            metadata: [
                'section' => 'Configuration',
                'kind' => 'runtime',
            ],
            secretNames: [],
            applyStrategy: 'component_runtime_yaml',
        );
    }

    public function loadData(): object
    {
        $data = new RollingConfigurationRoleRuntimeData();
        $runtime = $this->runtimeManifest();
        $role = is_array($runtime['role'] ?? null) ? $runtime['role'] : [];

        $data->enabled = (bool) ($role['enabled'] ?? true);
        $data->policyNamespace = (string) ($role['policy_namespace'] ?? $data->policyNamespace);
        $data->adminNamespace = (string) ($role['admin_namespace'] ?? $data->adminNamespace);
        $data->auditNamespace = (string) ($role['audit_namespace'] ?? $data->auditNamespace);
        $data->opsDir = (string) ($role['ops_dir'] ?? $data->opsDir);
        $data->sdkNamespace = (string) ($role['sdk_namespace'] ?? $data->sdkNamespace);

        return $data;
    }

    public function save(object $data, array $context = []): array
    {
        $payload = $this->assertData($data);
        $values = $this->stateRows($payload, 'pending');
        $masked = [
            'enabled' => $payload->enabled,
            'policy_namespace' => $payload->policyNamespace,
            'admin_namespace' => $payload->adminNamespace,
            'audit_namespace' => $payload->auditNamespace,
            'ops_dir' => $payload->opsDir,
            'sdk_namespace' => $payload->sdkNamespace,
        ];

        return $this->applyService->save($this->descriptor(), (string) ($context['actor'] ?? 'system'), $values, $masked, []);
    }

    public function apply(object $data, array $context = []): array
    {
        $payload = $this->assertData($data);
        $patch = $this->runtimePatch($payload);
        $write = $this->fileWriter->write(
            $this->projectDir.'/../Rolling',
            'config/component/runtime.yaml',
            $patch,
            $this->descriptor()->writableFiles,
        );

        $status = 'applied' === $write['status'] ? 'applied' : 'failed';
        $values = $this->stateRows($payload, $status);

        return $this->applyService->apply(
            $this->descriptor(),
            (string) ($context['actor'] ?? 'system'),
            $values,
            $patch,
            [],
            [[
                'path' => $write['path'],
                'backup_path' => $write['backup_path'],
                'status' => $write['status'],
                'message' => $write['message'],
            ]],
            [],
            'applied' === $write['status'] ? null : $write['message'],
            $status,
        );
    }

    private function assertData(object $data): RollingConfigurationRoleRuntimeData
    {
        if (!$data instanceof RollingConfigurationRoleRuntimeData) {
            throw new \InvalidArgumentException('Rolling role runtime config expects RollingConfigurationRoleRuntimeData.');
        }

        return $data;
    }

    /** @return array<string, mixed> */
    private function runtimeManifest(): array
    {
        $path = $this->projectDir.'/../Rolling/config/component/runtime.yaml';
        $parsed = is_file($path) ? Yaml::parseFile($path) : [];

        return is_array($parsed) ? $parsed : [];
    }

    /**
     * @return array<string, mixed>
     */
    private function runtimePatch(RollingConfigurationRoleRuntimeData $data): array
    {
        return [
            'role' => [
                'enabled' => $data->enabled,
                'policy_namespace' => $data->policyNamespace,
                'admin_namespace' => $data->adminNamespace,
                'audit_namespace' => $data->auditNamespace,
                'ops_dir' => $data->opsDir,
                'sdk_namespace' => $data->sdkNamespace,
            ],
        ];
    }

    /**
     * @return array<string, array{fieldType:string, secret:bool, current:?string, pending:?string, masked:?string, status:string}>
     */
    private function stateRows(RollingConfigurationRoleRuntimeData $data, string $status): array
    {
        return [
            'enabled' => ['fieldType' => 'boolean', 'secret' => false, 'current' => $data->enabled ? '1' : '0', 'pending' => $data->enabled ? '1' : '0', 'masked' => null, 'status' => $status],
            'policy_namespace' => ['fieldType' => 'string', 'secret' => false, 'current' => $data->policyNamespace, 'pending' => $data->policyNamespace, 'masked' => null, 'status' => $status],
            'admin_namespace' => ['fieldType' => 'string', 'secret' => false, 'current' => $data->adminNamespace, 'pending' => $data->adminNamespace, 'masked' => null, 'status' => $status],
            'audit_namespace' => ['fieldType' => 'string', 'secret' => false, 'current' => $data->auditNamespace, 'pending' => $data->auditNamespace, 'masked' => null, 'status' => $status],
            'ops_dir' => ['fieldType' => 'string', 'secret' => false, 'current' => $data->opsDir, 'pending' => $data->opsDir, 'masked' => null, 'status' => $status],
            'sdk_namespace' => ['fieldType' => 'string', 'secret' => false, 'current' => $data->sdkNamespace, 'pending' => $data->sdkNamespace, 'masked' => null, 'status' => $status],
        ];
    }
}
