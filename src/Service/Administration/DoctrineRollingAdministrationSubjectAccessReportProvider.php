<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\Entity\Acl\RollingAclRule;
use App\Rolling\Entity\Acl\RollingRole;
use App\Rolling\Entity\Acl\RollingRoleHierarchy;
use App\Rolling\Entity\Acl\RollingRolePermission;
use App\Rolling\Entity\Acl\RollingSubjectRoleAssignment;
use App\Rolling\ServiceInterface\Administration\RollingAdministrationPermissionCatalogInterface;
use App\Rolling\ServiceInterface\Administration\RollingAdministrationPermissionDecisionServiceInterface;
use App\Rolling\ServiceInterface\Administration\RollingAdministrationSubjectAccessReportProviderInterface;
use App\Rolling\Value\Administration\RollingAdministrationSubjectAccessReport;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine-backed subject access report for operator bootstrap/debug workflows.
 */
final class DoctrineRollingAdministrationSubjectAccessReportProvider implements RollingAdministrationSubjectAccessReportProviderInterface
{
    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly RollingAdministrationPermissionCatalogInterface $permissionCatalog,
        private readonly RollingAdministrationPermissionDecisionServiceInterface $decisionService,
    ) {
    }

    public function reportFor(string $subjectIdentifier, string $scope = 'administering:global'): RollingAdministrationSubjectAccessReport
    {
        $subjectIdentifier = trim($subjectIdentifier);
        $scope = $this->normalizeScope($scope);

        $assignedRoles = $this->assignedRoles($subjectIdentifier, $scope);
        $effectiveRoles = $this->effectiveRoles($this->effectiveSeedRoleKeys($assignedRoles));

        $directRules = $this->directRules($subjectIdentifier, $scope);
        $rolePermissions = $this->rolePermissions($effectiveRoles, $scope);
        $cataloguedPermissions = $this->permissionCatalog->permissions();

        $grantedPermissions = [];
        $deniedPermissions = [];
        foreach ($cataloguedPermissions as $permission) {
            $permission = trim($permission);
            if ('' === $permission) {
                continue;
            }

            if ($this->decisionService->isGranted($subjectIdentifier, $permission, $scope)) {
                $grantedPermissions[] = $permission;
            } else {
                $deniedPermissions[] = $permission;
            }
        }

        sort($grantedPermissions);
        sort($deniedPermissions);
        sort($cataloguedPermissions);

        return new RollingAdministrationSubjectAccessReport(
            $subjectIdentifier,
            $scope,
            $assignedRoles,
            $effectiveRoles,
            $directRules,
            $rolePermissions,
            $grantedPermissions,
            $deniedPermissions,
            $cataloguedPermissions,
        );
    }

    /** @return list<array<string, mixed>> */
    private function assignedRoles(string $subjectIdentifier, string $scope): array
    {
        $manager = $this->registry->getManagerForClass(RollingSubjectRoleAssignment::class);
        if (null === $manager || '' === $subjectIdentifier) {
            return [];
        }

        $rows = [];
        $assignments = $manager->getRepository(RollingSubjectRoleAssignment::class)->findBy(['subjectIdentifier' => $subjectIdentifier]);
        foreach ($assignments as $assignment) {
            if (!$assignment instanceof RollingSubjectRoleAssignment) {
                continue;
            }

            $roleKey = trim($assignment->roleKey());
            $assignmentScope = $this->normalizeScope($assignment->scopeKey());
            $rows[] = [
                'role_key' => $roleKey,
                'scope_key' => $assignmentScope,
                'matches_requested_scope' => $this->scopeMatches($assignmentScope, $scope),
                'role_enabled' => '' !== $roleKey && $this->roleEnabled($roleKey),
                'assigned_at' => $assignment->assignedAt()->format(DATE_ATOM),
            ];
        }

        usort($rows, static fn (array $a, array $b): int => [$a['role_key'], $a['scope_key']] <=> [$b['role_key'], $b['scope_key']]);

        return $rows;
    }

    /**
     * @param list<array<string, mixed>> $assignedRoles
     *
     * @return list<string>
     */
    private function effectiveSeedRoleKeys(array $assignedRoles): array
    {
        $roleKeys = [];
        foreach ($assignedRoles as $row) {
            if (true !== ($row['matches_requested_scope'] ?? false) || true !== ($row['role_enabled'] ?? false)) {
                continue;
            }

            $roleKey = is_string($row['role_key'] ?? null) ? trim($row['role_key']) : '';
            if ('' !== $roleKey) {
                $roleKeys[$roleKey] = true;
            }
        }

        $roleKeys = array_keys($roleKeys);
        sort($roleKeys);

        return $roleKeys;
    }

    /** @param list<string> $roleKeys */
    private function effectiveRoles(array $roleKeys): array
    {
        $roleKeys = array_values(array_unique(array_filter($roleKeys, static fn (string $roleKey): bool => '' !== trim($roleKey))));
        if ([] === $roleKeys) {
            return [];
        }

        $manager = $this->registry->getManagerForClass(RollingRoleHierarchy::class);
        if (null === $manager) {
            sort($roleKeys);

            return $roleKeys;
        }

        $known = array_fill_keys($roleKeys, true);
        $frontier = $roleKeys;
        while ([] !== $frontier) {
            $next = [];
            foreach ($frontier as $parentRoleKey) {
                $edges = $manager->getRepository(RollingRoleHierarchy::class)->findBy([
                    'parentRoleKey' => $parentRoleKey,
                    'enabled' => true,
                ]);

                foreach ($edges as $edge) {
                    if (!$edge instanceof RollingRoleHierarchy) {
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

            $frontier = $next;
        }

        $roles = array_keys($known);
        sort($roles);

        return $roles;
    }

    /** @return list<array<string, mixed>> */
    private function directRules(string $subjectIdentifier, string $scope): array
    {
        $manager = $this->registry->getManagerForClass(RollingAclRule::class);
        if (null === $manager || '' === $subjectIdentifier) {
            return [];
        }

        $rows = [];
        $rules = $manager->getRepository(RollingAclRule::class)->findBy(['subjectIdentifier' => $subjectIdentifier]);
        foreach ($rules as $rule) {
            if (!$rule instanceof RollingAclRule) {
                continue;
            }

            $ruleScope = $this->normalizeScope($rule->scopeKey());
            $rows[] = [
                'permission_key' => $rule->permissionKey(),
                'scope_key' => $ruleScope,
                'effect' => $rule->effect(),
                'enabled' => $rule->enabled(),
                'matches_requested_scope' => $this->scopeMatches($ruleScope, $scope),
            ];
        }

        usort($rows, static fn (array $a, array $b): int => [$a['permission_key'], $a['scope_key'], $a['effect']] <=> [$b['permission_key'], $b['scope_key'], $b['effect']]);

        return $rows;
    }

    /**
     * @param list<string> $roleKeys
     *
     * @return list<array<string, mixed>>
     */
    private function rolePermissions(array $roleKeys, string $scope): array
    {
        $manager = $this->registry->getManagerForClass(RollingRolePermission::class);
        if (null === $manager || [] === $roleKeys) {
            return [];
        }

        $rows = [];
        foreach ($roleKeys as $roleKey) {
            $grants = $manager->getRepository(RollingRolePermission::class)->findBy(['roleKey' => $roleKey]);
            foreach ($grants as $grant) {
                if (!$grant instanceof RollingRolePermission) {
                    continue;
                }

                $scopePattern = $this->normalizeScope($grant->scopePattern());
                $rows[] = [
                    'role_key' => $grant->roleKey(),
                    'permission_key' => $grant->permissionKey(),
                    'scope_pattern' => $scopePattern,
                    'effect' => $grant->effect(),
                    'matches_requested_scope' => $this->scopeMatches($scopePattern, $scope),
                ];
            }
        }

        usort($rows, static fn (array $a, array $b): int => [$a['role_key'], $a['permission_key'], $a['scope_pattern']] <=> [$b['role_key'], $b['permission_key'], $b['scope_pattern']]);

        return $rows;
    }

    private function roleEnabled(string $roleKey): bool
    {
        $manager = $this->registry->getManagerForClass(RollingRole::class);
        if (null === $manager) {
            return false;
        }

        $role = $manager->getRepository(RollingRole::class)->findOneBy(['roleKey' => $roleKey]);

        return $role instanceof RollingRole && $role->enabled();
    }

    private function normalizeScope(string $scope): string
    {
        $scope = trim($scope);

        return '' !== $scope ? $scope : 'administering:global';
    }

    private function scopeMatches(string $pattern, string $scope): bool
    {
        $pattern = $this->normalizeScope($pattern);
        $scope = $this->normalizeScope($scope);

        if ('*' === $pattern || $pattern === $scope) {
            return true;
        }

        if ('global' === $pattern && str_ends_with($scope, ':global')) {
            return true;
        }

        if (str_ends_with($pattern, '*')) {
            return str_starts_with($scope, rtrim($pattern, '*'));
        }

        return false;
    }
}
