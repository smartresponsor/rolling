<?php

declare(strict_types=1);

namespace App\Rolling\Service\Cruding;

use App\Rolling\Entity\Role\RoleAclMutationExecutionEventEntity;
use App\Rolling\Entity\Role\RoleAclRuleEntity;
use App\Rolling\Entity\Role\RoleEntity;
use App\Rolling\Entity\Role\RoleHierarchyEntity;
use App\Rolling\Entity\Role\RolePermissionEntity;
use App\Rolling\Entity\Role\RoleSubjectAssignmentEntity;
use App\Rolling\ServiceInterface\Cruding\RollingCrudResourceDefinitionProviderInterface;
use App\Rolling\Value\Cruding\RollingCrudResourceDefinition;

/**
 * Defines Rolling resources that should move from EasyAdmin CRUD controllers
 * into Cruding resource providers.
 */
final readonly class RollingCrudResourceDefinitionProvider implements RollingCrudResourceDefinitionProviderInterface
{
    public function definitions(): array
    {
        return [
            new RollingCrudResourceDefinition(
                'rolling.role',
                RoleEntity::class,
                'Rolling role',
                ['index', 'show', 'new', 'edit'],
                [
                    $this->field('id', 'integer', false),
                    $this->field('roleKey', 'string'),
                    $this->field('label', 'string'),
                    $this->field('systemRole', 'boolean'),
                    $this->field('enabled', 'boolean'),
                ],
                ['legacy_controller' => 'RollingRoleCrudController'],
            ),
            new RollingCrudResourceDefinition(
                'rolling.role-permission',
                RolePermissionEntity::class,
                'Rolling role permission',
                ['index', 'show', 'new', 'edit'],
                [
                    $this->field('id', 'integer', false),
                    $this->field('roleKey', 'string'),
                    $this->field('permissionKey', 'string'),
                    $this->field('scopePattern', 'string'),
                    $this->field('effect', 'choice', true, ['allow', 'deny']),
                ],
                ['legacy_controller' => 'RollingRolePermissionCrudController'],
            ),
            new RollingCrudResourceDefinition(
                'rolling.subject-assignment',
                RoleSubjectAssignmentEntity::class,
                'Rolling subject assignment',
                ['index', 'show', 'new', 'edit'],
                [
                    $this->field('id', 'integer', false),
                    $this->field('subjectIdentifier', 'string'),
                    $this->field('roleKey', 'string'),
                    $this->field('scopeKey', 'string'),
                    $this->field('assignedAt', 'datetime', false),
                ],
                ['legacy_controller' => 'RollingSubjectRoleAssignmentCrudController'],
            ),
            new RollingCrudResourceDefinition(
                'rolling.acl-rule',
                RoleAclRuleEntity::class,
                'Rolling ACL rule',
                ['index', 'show', 'new', 'edit'],
                [
                    $this->field('id', 'integer', false),
                    $this->field('subjectIdentifier', 'string'),
                    $this->field('permissionKey', 'string'),
                    $this->field('scopeKey', 'string'),
                    $this->field('effect', 'choice', true, ['allow', 'deny']),
                    $this->field('conditions', 'json', false),
                    $this->field('enabled', 'boolean'),
                ],
                ['legacy_controller' => 'RollingAclRuleCrudController'],
            ),
            new RollingCrudResourceDefinition(
                'rolling.role-hierarchy',
                RoleHierarchyEntity::class,
                'Rolling role hierarchy edge',
                ['index', 'show'],
                [
                    $this->field('id', 'integer', false),
                    $this->field('parentRoleKey', 'string'),
                    $this->field('childRoleKey', 'string'),
                    $this->field('enabled', 'boolean'),
                ],
                ['business_mutation_service' => 'RollingRoleHierarchyMutationServiceInterface'],
            ),
            new RollingCrudResourceDefinition(
                'rolling.acl-mutation-execution-event',
                RoleAclMutationExecutionEventEntity::class,
                'Rolling ACL mutation execution event',
                ['index', 'show'],
                [
                    $this->field('id', 'integer', false),
                    $this->field('requestKey', 'string'),
                    $this->field('status', 'string', false),
                    $this->field('succeeded', 'boolean', false),
                    $this->field('createdAt', 'datetime', false),
                ],
                ['legacy_controller' => 'RollingAclMutationExecutionEventCrudController'],
            ),
        ];
    }

    /** @return array<string, mixed> */
    private function field(string $name, string $type, bool $editable = true, array $choices = []): array
    {
        return array_filter([
            'name' => $name,
            'type' => $type,
            'editable' => $editable,
            'choices' => [] !== $choices ? $choices : null,
        ], static fn (mixed $value): bool => null !== $value);
    }
}
