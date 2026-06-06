<?php

declare(strict_types=1);

namespace App\Rolling\Service\Config;

use App\Configuring\ServiceInterface\Config\ConfigToolServiceInterface;
use App\Configuring\ServiceInterface\Config\ConfigVariableToolServiceInterface;
use App\Configuring\ServiceInterface\Config\ManagedConfigVariablesProviderInterface;
use App\Configuring\Value\Config\ConfigToolDescriptor;
use App\Configuring\Value\Config\ConfigVariable;
use App\Configuring\Value\Config\ConfigVariableType;
use App\Rolling\ServiceInterface\Administration\RollingRoleHierarchyMutationServiceInterface;

/**
 * Configuring-facing reviewed mutation tool for Rolling role hierarchy edges.
 *
 * This exposes a generic managed-variable form to Administering while delegating
 * the actual hierarchy review/apply semantics to Rolling's mutation service.
 */
final readonly class RollingConfigurationRoleHierarchyMutationService implements ConfigToolServiceInterface, ManagedConfigVariablesProviderInterface, ConfigVariableToolServiceInterface
{
    public function __construct(private RollingRoleHierarchyMutationServiceInterface $mutationService)
    {
    }

    public function descriptor(): ConfigToolDescriptor
    {
        return new ConfigToolDescriptor(
            applicationCode: 'Rolling',
            toolCode: 'rolling.role_hierarchy_mutation',
            label: 'Rolling Role Hierarchy Mutation',
            description: 'Review and apply role hierarchy edge changes through Rolling-owned mutation semantics.',
            formClass: '',
            serviceClass: self::class,
            requiredPermission: 'administration.rolling.acl_mutation.apply',
            editableFields: [],
            sensitiveFields: [],
            readableFiles: [],
            writableFiles: [],
            metadata: [
                'section' => 'Rolling ACL',
                'kind' => 'role_hierarchy_mutation',
                'mutation_owner' => 'rolling',
                'direct_crud_editing' => false,
            ],
            secretNames: [],
            applyStrategy: 'rolling_reviewed_mutation',
        );
    }

    /** @return iterable<ConfigVariable> */
    public function managedVariables(): iterable
    {
        yield ConfigVariable::yaml('hierarchy_mutation.action', null, ConfigVariableType::ENUM)
            ->withLabel('Action')
            ->withChoices(['add_edge', 'enable_edge', 'disable_edge'])
            ->required();
        yield ConfigVariable::yaml('hierarchy_mutation.parent_role_key')
            ->withLabel('Parent role key')
            ->required();
        yield ConfigVariable::yaml('hierarchy_mutation.child_role_key')
            ->withLabel('Child role key')
            ->required();
        yield ConfigVariable::yaml('hierarchy_mutation.justification')
            ->withLabel('Justification')
            ->required();
        yield ConfigVariable::yaml('hierarchy_mutation.review_confirmed', null, ConfigVariableType::BOOL)
            ->withLabel('I reviewed this hierarchy mutation')
            ->required(false);
    }

    public function loadVariableData(): array
    {
        return [
            'hierarchy_mutation.action' => 'add_edge',
            'hierarchy_mutation.parent_role_key' => '',
            'hierarchy_mutation.child_role_key' => '',
            'hierarchy_mutation.justification' => '',
            'hierarchy_mutation.review_confirmed' => false,
        ];
    }

    public function saveVariables(array $variables, array $context = []): array
    {
        $review = $this->mutationService->review($variables);

        return [
            'status' => $review['status'],
            'messages' => $review['messages'],
            'masked_changes' => $review['review'],
            'file_changes' => [],
            'secret_changes' => [],
        ];
    }

    public function applyVariables(array $variables, array $context = []): array
    {
        if (true !== (bool) ($variables['hierarchy_mutation.review_confirmed'] ?? false)) {
            $review = $this->mutationService->review($variables);

            return [
                'status' => 'review_required',
                'messages' => array_merge(['Confirm review before applying a role hierarchy mutation.'], $review['messages']),
                'masked_changes' => $review['review'],
                'file_changes' => [],
                'secret_changes' => [],
            ];
        }

        $result = $this->mutationService->apply($variables, $context);

        return [
            'status' => $result['status'],
            'messages' => $result['messages'],
            'masked_changes' => $result['metadata'],
            'file_changes' => [],
            'secret_changes' => [],
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
