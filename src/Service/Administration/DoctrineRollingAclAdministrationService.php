<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\Entity\Role\RoleAclRuleEntity;
use App\Rolling\Entity\Role\RolePermissionEntity;
use App\Rolling\Entity\Role\RoleSubjectAssignmentEntity;
use App\Rolling\Repository\Role\RoleAclRuleRepository;
use App\Rolling\Repository\Role\RolePermissionRepository;
use App\Rolling\Repository\Role\RoleRepository;
use App\Rolling\Repository\Role\RoleSubjectAssignmentRepository;
use App\Rolling\ServiceInterface\Administration\RollingAclAdministrationServiceInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationAuditRecorderInterface;
use App\Rolling\ServiceInterface\Administration\RollingAclMutationValidatorInterface;
use App\Rolling\Value\Administration\RollingAclMutationAuditEvent;
use App\Rolling\Value\Administration\RollingAclMutationRequest;
use App\Rolling\Value\Administration\RollingAclMutationResult;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine-backed ACL mutation service for the application ACL hierarchy.
 *
 * This is the first executable Rolling administration implementation. It keeps
 * Rolling as the authorization owner while allowing Administering to request
 * controlled mutations through a stable service boundary.
 */
final class DoctrineRollingAclAdministrationService implements RollingAclAdministrationServiceInterface
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly RollingAclMutationValidatorInterface $mutationValidator,
        private readonly RollingAclMutationAuditRecorderInterface $auditRecorder,
        private readonly RoleRepository $roleRepository,
        private readonly RolePermissionRepository $permissionRepository,
        private readonly RoleSubjectAssignmentRepository $assignmentRepository,
        private readonly RolePermissionRepository $rolePermissionRepository,
        private readonly RoleAclRuleRepository $aclRuleRepository,
    ) {
    }

    public function mutate(RollingAclMutationRequest $request): RollingAclMutationResult
    {
        $validation = $this->mutationValidator->validate($request);
        if (!$validation->valid()) {
            return $this->record($request, RollingAclMutationResult::rejected(
                'Rolling ACL mutation request failed validation.',
                ['mutation_type' => $request->mutationType(), 'violations' => $validation->violations()],
            ));
        }

        $result = match ($request->mutationType()) {
            'role.assign' => $this->assignRole($request),
            'role.revoke' => $this->revokeRole($request),
            'permission.grant' => $this->grantPermission($request),
            'permission.revoke' => $this->revokePermission($request),
            'acl.allow' => $this->createAclRule($request, 'allow'),
            'acl.deny' => $this->createAclRule($request, 'deny'),
            default => RollingAclMutationResult::rejected('Unsupported Rolling ACL mutation type.', ['mutation_type' => $request->mutationType()]),
        };

        return $this->record($request, $result);
    }

    private function assignRole(RollingAclMutationRequest $request): RollingAclMutationResult
    {
        $roleKey = $this->clean($request->permissionOrRoleKey());
        $role = $this->roleRepository->requireEnabled($roleKey);
        if (null === $role) {
            return RollingAclMutationResult::rejected('Role does not exist or is disabled.', ['role_key' => $roleKey]);
        }

        $existing = $this->assignmentRepository->findOneAssignment($request->subjectIdentifier(), $roleKey, $request->scopeKey());
        if (null !== $existing) {
            return RollingAclMutationResult::success('Role assignment already exists.', ['assignment_id' => $existing->id(), 'role_key' => $roleKey]);
        }

        $assignment = new RoleSubjectAssignmentEntity($request->subjectIdentifier(), $roleKey, $request->scopeKey());
        $this->assignmentRepository->save($assignment);
        $this->flushManagerFor(RoleSubjectAssignmentEntity::class);

        return RollingAclMutationResult::success('Role assignment persisted.', ['assignment_id' => $assignment->id(), 'role_key' => $roleKey]);
    }

    private function revokeRole(RollingAclMutationRequest $request): RollingAclMutationResult
    {
        $roleKey = $this->clean($request->permissionOrRoleKey());
        $assignment = $this->assignmentRepository->findOneAssignment($request->subjectIdentifier(), $roleKey, $request->scopeKey());
        if (null === $assignment) {
            return RollingAclMutationResult::success('Role assignment was already absent.', ['role_key' => $roleKey]);
        }

        $assignmentId = $assignment->id();
        $this->assignmentRepository->remove($assignment);
        $this->flushManagerFor(RoleSubjectAssignmentEntity::class);

        return RollingAclMutationResult::success('Role assignment removed.', ['assignment_id' => $assignmentId, 'role_key' => $roleKey]);
    }

    private function grantPermission(RollingAclMutationRequest $request): RollingAclMutationResult
    {
        $roleKey = $this->clean($request->subjectIdentifier());
        $permissionKey = $this->clean($request->permissionOrRoleKey());

        if (null === $this->roleRepository->requireEnabled($roleKey)) {
            return RollingAclMutationResult::rejected('Role does not exist or is disabled.', ['role_key' => $roleKey]);
        }

        if (null === $this->permissionRepository->findOneByPermissionKey($permissionKey)) {
            return RollingAclMutationResult::rejected('Permission does not exist.', ['permission_key' => $permissionKey]);
        }

        $existing = $this->rolePermissionRepository->findOneGrant($roleKey, $permissionKey, $request->scopeKey());
        if (null !== $existing) {
            return RollingAclMutationResult::success('Role permission grant already exists.', ['grant_id' => $existing->id()]);
        }

        $grant = new RolePermissionEntity($roleKey, $permissionKey, $request->scopeKey());
        $this->rolePermissionRepository->save($grant);
        $this->flushManagerFor(RolePermissionEntity::class);

        return RollingAclMutationResult::success('Role permission grant persisted.', ['grant_id' => $grant->id(), 'role_key' => $roleKey, 'permission_key' => $permissionKey]);
    }

    private function revokePermission(RollingAclMutationRequest $request): RollingAclMutationResult
    {
        $roleKey = $this->clean($request->subjectIdentifier());
        $permissionKey = $this->clean($request->permissionOrRoleKey());
        $grant = $this->rolePermissionRepository->findOneGrant($roleKey, $permissionKey, $request->scopeKey());
        if (null === $grant) {
            return RollingAclMutationResult::success('Role permission grant was already absent.', ['role_key' => $roleKey, 'permission_key' => $permissionKey]);
        }

        $grantId = $grant->id();
        $this->rolePermissionRepository->remove($grant);
        $this->flushManagerFor(RolePermissionEntity::class);

        return RollingAclMutationResult::success('Role permission grant removed.', ['grant_id' => $grantId, 'role_key' => $roleKey, 'permission_key' => $permissionKey]);
    }

    private function createAclRule(RollingAclMutationRequest $request, string $effect): RollingAclMutationResult
    {
        $permissionKey = $this->clean($request->permissionOrRoleKey());
        if (null === $this->permissionRepository->findOneByPermissionKey($permissionKey)) {
            return RollingAclMutationResult::rejected('Permission does not exist.', ['permission_key' => $permissionKey]);
        }

        $rule = new RoleAclRuleEntity($request->subjectIdentifier(), $permissionKey, $request->scopeKey(), $effect);
        $this->aclRuleRepository->save($rule);
        $this->flushManagerFor(RoleAclRuleEntity::class);

        return RollingAclMutationResult::success('ACL rule persisted.', ['rule_id' => $rule->id(), 'effect' => $effect, 'permission_key' => $permissionKey]);
    }

    /** @param class-string $entityClass */
    private function flushManagerFor(string $entityClass): void
    {
        $manager = $this->registry->getManagerForClass($entityClass);
        if (null === $manager) {
            throw new \RuntimeException(sprintf('No Doctrine manager configured for %s.', $entityClass));
        }

        $manager->flush();
    }

    private function record(RollingAclMutationRequest $request, RollingAclMutationResult $result): RollingAclMutationResult
    {
        $this->auditRecorder->record(RollingAclMutationAuditEvent::fromResult($request, $result));

        return $result;
    }

    private function clean(string $value): string
    {
        return trim($value);
    }
}
