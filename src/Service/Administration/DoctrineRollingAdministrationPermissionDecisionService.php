<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\Entity\Acl\RollingAclRule;
use App\Rolling\Entity\Acl\RollingRole;
use App\Rolling\Entity\Acl\RollingRoleHierarchy;
use App\Rolling\Entity\Acl\RollingRolePermission;
use App\Rolling\Entity\Acl\RollingSubjectRoleAssignment;
use App\Rolling\ServiceInterface\Administration\RollingAdministrationPermissionDecisionServiceInterface;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Doctrine-backed administration permission decision service.
 *
 * This service is intentionally narrower than the generic PDP pipeline: it evaluates
 * the Rolling ACL administration tables directly so Administering does not depend on
 * the permissive demo PDP when protecting state-changing admin surfaces.
 */
final class DoctrineRollingAdministrationPermissionDecisionService implements RollingAdministrationPermissionDecisionServiceInterface
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    public function isGranted(string $subjectIdentifier, string $permission, string $scope = 'global', array $context = []): bool
    {
        $subjectIdentifier = trim($subjectIdentifier);
        $permission = trim($permission);
        $scope = $this->normalizeScope($scope);

        if ('' === $subjectIdentifier || '' === $permission) {
            return false;
        }

        $directRuleDecision = $this->directRuleDecision($subjectIdentifier, $permission, $scope);
        if (null !== $directRuleDecision) {
            return $directRuleDecision;
        }

        $roleKeys = $this->roleKeysForSubject($subjectIdentifier, $scope);
        if ([] === $roleKeys) {
            return false;
        }

        return $this->rolePermissionDecision($roleKeys, $permission, $scope) ?? false;
    }

    private function directRuleDecision(string $subjectIdentifier, string $permission, string $scope): ?bool
    {
        $manager = $this->registry->getManagerForClass(RollingAclRule::class);
        if (null === $manager) {
            return null;
        }

        $rules = $manager->getRepository(RollingAclRule::class)->findBy([
            'subjectIdentifier' => $subjectIdentifier,
            'permissionKey' => $permission,
            'enabled' => true,
        ]);

        $allow = false;
        foreach ($rules as $rule) {
            if (!$rule instanceof RollingAclRule || !$this->scopeMatches($rule->scopeKey(), $scope)) {
                continue;
            }

            if ('deny' === $rule->effect()) {
                return false;
            }

            if ('allow' === $rule->effect()) {
                $allow = true;
            }
        }

        return $allow ? true : null;
    }

    /** @return list<string> */
    private function roleKeysForSubject(string $subjectIdentifier, string $scope): array
    {
        $manager = $this->registry->getManagerForClass(RollingSubjectRoleAssignment::class);
        if (null === $manager) {
            return [];
        }

        $assignments = $manager->getRepository(RollingSubjectRoleAssignment::class)->findBy([
            'subjectIdentifier' => $subjectIdentifier,
        ]);

        $roleKeys = [];
        foreach ($assignments as $assignment) {
            if (!$assignment instanceof RollingSubjectRoleAssignment || !$this->scopeMatches($assignment->scopeKey(), $scope)) {
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
        $manager = $this->registry->getManagerForClass(RollingRole::class);
        if (null === $manager) {
            return false;
        }

        $role = $manager->getRepository(RollingRole::class)->findOneBy(['roleKey' => $roleKey]);

        return $role instanceof RollingRole && $role->enabled();
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

        $manager = $this->registry->getManagerForClass(RollingRoleHierarchy::class);
        if (null === $manager) {
            return array_values(array_unique($roleKeys));
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

        return array_keys($known);
    }

    /** @param list<string> $roleKeys */
    private function rolePermissionDecision(array $roleKeys, string $permission, string $scope): ?bool
    {
        $manager = $this->registry->getManagerForClass(RollingRolePermission::class);
        if (null === $manager) {
            return null;
        }

        $allow = false;
        foreach ($roleKeys as $roleKey) {
            $grants = $manager->getRepository(RollingRolePermission::class)->findBy([
                'roleKey' => $roleKey,
                'permissionKey' => $permission,
            ]);

            foreach ($grants as $grant) {
                if (!$grant instanceof RollingRolePermission || !$this->scopeMatches($grant->scopePattern(), $scope)) {
                    continue;
                }

                if ('deny' === $grant->effect()) {
                    return false;
                }

                if ('allow' === $grant->effect()) {
                    $allow = true;
                }
            }
        }

        return $allow ? true : null;
    }

    private function normalizeScope(string $scope): string
    {
        $scope = trim($scope);

        return '' !== $scope ? $scope : 'global';
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
