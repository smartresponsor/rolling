<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\Entity\Role\RoleAclRuleEntity;
use App\Rolling\Entity\Role\RoleEntity;
use App\Rolling\Entity\Role\RoleHierarchyEntity;
use App\Rolling\Entity\Role\RolePermissionEntity;
use App\Rolling\Entity\Role\RoleSubjectAssignmentEntity;
use App\Rolling\ServiceInterface\Administration\RollingFieldAccessDecisionServiceInterface;
use App\Rolling\Value\Administration\RollingFieldAccessDecision;
use App\Rolling\Value\Administration\RollingFieldAccessDecisionRequest;
use App\Rolling\Value\Administration\RollingFieldAccessScopeSet;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine-backed field access decision service for Managing field-level permissions.
 *
 * It uses the existing Rolling ACL tables and a canonical scope chain. Direct subject rules are evaluated before
 * role grants to preserve current administration permission semantics. Inside each layer, explicit deny wins.
 */
final class DoctrineRollingFieldAccessDecisionService implements RollingFieldAccessDecisionServiceInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    public function decide(RollingFieldAccessDecisionRequest $request): RollingFieldAccessDecision
    {
        $subjectIdentifier = trim((string) $request->subjectIdentifier);
        $permission = trim($request->permissionKey);
        if ('' === $subjectIdentifier || '' === $permission) {
            return RollingFieldAccessDecision::deny('missing subject or permission');
        }

        $scopeSet = RollingFieldAccessScopeSet::fromRequest($request);
        $direct = $this->directRuleDecision($subjectIdentifier, $permission, $scopeSet->scopes(), $request);
        if (null !== $direct) {
            return $direct;
        }

        $roleKeys = $this->roleKeysForSubject($subjectIdentifier, $scopeSet->scopes());
        if ([] === $roleKeys) {
            return RollingFieldAccessDecision::abstain('no matching role assignment');
        }

        return $this->rolePermissionDecision($roleKeys, $permission, $scopeSet->scopes())
            ?? RollingFieldAccessDecision::abstain('no matching field access grant');
    }

    /**
     * @param non-empty-list<string> $scopes
     */
    private function directRuleDecision(
        string $subjectIdentifier,
        string $permission,
        array $scopes,
        RollingFieldAccessDecisionRequest $request,
    ): ?RollingFieldAccessDecision {
        $manager = $this->registry->getManagerForClass(RoleAclRuleEntity::class);
        if (null === $manager) {
            return null;
        }

        $rules = $manager->getRepository(RoleAclRuleEntity::class)->findBy([
            'subjectIdentifier' => $subjectIdentifier,
            'permissionKey' => $permission,
            'enabled' => true,
        ]);

        $allow = null;
        foreach ($rules as $rule) {
            if (!$rule instanceof RoleAclRuleEntity || !$this->matchesAnyScope($rule->scopeKey(), $scopes)) {
                continue;
            }

            if (!$this->conditionsMatch($rule->conditions(), $request)) {
                continue;
            }

            if ('deny' === $rule->effect()) {
                return RollingFieldAccessDecision::deny('matched direct field deny rule');
            }

            if ('allow' === $rule->effect()) {
                $allow = RollingFieldAccessDecision::allow('matched direct field allow rule');
            }
        }

        return $allow;
    }

    /**
     * @param non-empty-list<string> $scopes
     *
     * @return list<string>
     */
    private function roleKeysForSubject(string $subjectIdentifier, array $scopes): array
    {
        $manager = $this->registry->getManagerForClass(RoleSubjectAssignmentEntity::class);
        if (null === $manager) {
            return [];
        }

        $assignments = $manager->getRepository(RoleSubjectAssignmentEntity::class)->findBy([
            'subjectIdentifier' => $subjectIdentifier,
        ]);

        $roleKeys = [];
        foreach ($assignments as $assignment) {
            if (!$assignment instanceof RoleSubjectAssignmentEntity) {
                continue;
            }

            if (!$this->matchesAnyScope($assignment->scopeKey(), $scopes)) {
                continue;
            }

            $roleKey = trim($assignment->roleKey());
            if ('' !== $roleKey && $this->roleEnabled($roleKey)) {
                $roleKeys[$roleKey] = true;
            }
        }

        return $this->withInheritedRoles(array_keys($roleKeys));
    }

    private function roleEnabled(string $roleKey): bool
    {
        $manager = $this->registry->getManagerForClass(RoleEntity::class);
        if (null === $manager) {
            return false;
        }

        $role = $manager->getRepository(RoleEntity::class)->findOneBy(['roleKey' => $roleKey]);

        return $role instanceof RoleEntity && $role->enabled();
    }

    /**
     * @param list<string> $roleKeys
     *
     * @return list<string>
     */
    private function withInheritedRoles(array $roleKeys): array
    {
        if ([] === $roleKeys) {
            return [];
        }

        $manager = $this->registry->getManagerForClass(RoleHierarchyEntity::class);
        if (null === $manager) {
            return array_values(array_unique($roleKeys));
        }

        $known = array_fill_keys($roleKeys, true);
        $frontier = $roleKeys;
        while ([] !== $frontier) {
            $frontier = $this->collectChildRoles($frontier, $known);
        }

        return array_keys($known);
    }

    /**
     * @param list<string>        $frontier
     * @param array<string, true> $known
     *
     * @return list<string>
     */
    private function collectChildRoles(array $frontier, array &$known): array
    {
        $manager = $this->registry->getManagerForClass(RoleHierarchyEntity::class);
        if (null === $manager) {
            return [];
        }

        $next = [];
        foreach ($frontier as $parentRoleKey) {
            $edges = $manager->getRepository(RoleHierarchyEntity::class)->findBy([
                'parentRoleKey' => $parentRoleKey,
                'enabled' => true,
            ]);

            foreach ($edges as $edge) {
                if (!$edge instanceof RoleHierarchyEntity) {
                    continue;
                }

                $childRoleKey = trim($edge->childRoleKey());
                if ('' === $childRoleKey || isset($known[$childRoleKey]) || !$this->roleEnabled($childRoleKey)) {
                    continue;
                }

                $known[$childRoleKey] = true;
                $next[] = $childRoleKey;
            }
        }

        return $next;
    }

    /**
     * @param list<string>           $roleKeys
     * @param non-empty-list<string> $scopes
     */
    private function rolePermissionDecision(array $roleKeys, string $permission, array $scopes): ?RollingFieldAccessDecision
    {
        $manager = $this->registry->getManagerForClass(RolePermissionEntity::class);
        if (null === $manager) {
            return null;
        }

        $allow = null;
        foreach ($roleKeys as $roleKey) {
            $grants = $manager->getRepository(RolePermissionEntity::class)->findBy([
                'roleKey' => $roleKey,
                'permissionKey' => $permission,
            ]);

            foreach ($grants as $grant) {
                if (!$grant instanceof RolePermissionEntity || !$this->matchesAnyScope($grant->scopePattern(), $scopes)) {
                    continue;
                }

                if ('deny' === $grant->effect()) {
                    return RollingFieldAccessDecision::deny('matched role field deny grant');
                }

                if ('allow' === $grant->effect()) {
                    $allow = RollingFieldAccessDecision::allow('matched role field allow grant');
                }
            }
        }

        return $allow;
    }

    /**
     * @param non-empty-list<string> $scopes
     */
    private function matchesAnyScope(string $pattern, array $scopes): bool
    {
        foreach ($scopes as $scope) {
            if ($this->scopeMatches($pattern, $scope)) {
                return true;
            }
        }

        return false;
    }

    private function scopeMatches(string $pattern, string $scope): bool
    {
        $pattern = $this->normalizeScope($pattern);
        $scope = $this->normalizeScope($scope);

        if ('*' === $pattern || $pattern === $scope) {
            return true;
        }

        if (str_ends_with($pattern, '*')) {
            return str_starts_with($scope, rtrim($pattern, '*'));
        }

        return false;
    }

    private function normalizeScope(string $scope): string
    {
        $scope = trim($scope);

        return '' !== $scope ? $scope : 'global';
    }

    /** @param array<string, mixed> $conditions */
    private function conditionsMatch(array $conditions, RollingFieldAccessDecisionRequest $request): bool
    {
        if ([] === $conditions) {
            return true;
        }

        $attributes = $request->toAttributes();
        foreach ($conditions as $nameEntity => $expected) {
            if (!array_key_exists((string) $nameEntity, $attributes)) {
                return false;
            }

            if (!$this->conditionMatches($attributes[(string) $nameEntity], $expected)) {
                return false;
            }
        }

        return true;
    }

    private function conditionMatches(mixed $actual, mixed $expected): bool
    {
        if (is_array($expected)) {
            return in_array($actual, $expected, true);
        }

        return $actual === $expected;
    }
}
