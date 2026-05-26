<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\Entity\Acl\RollingPermission;
use App\Rolling\Entity\Acl\RollingRole;
use App\Rolling\Entity\Acl\RollingRoleHierarchy;
use App\Rolling\Entity\Acl\RollingRolePermission;
use App\Rolling\Entity\Acl\RollingSubjectRoleAssignment;
use App\Rolling\ServiceInterface\Administration\RollingAdministrationCatalogSyncServiceInterface;
use App\Rolling\ServiceInterface\Administration\RollingAdministrationPermissionCatalogInterface;
use App\Rolling\Value\Administration\RollingAdministrationCatalogSyncResult;
use App\Rolling\Value\Administration\RollingAdministrationPermissionDescriptor;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

/**
 * Synchronizes the safe runtime administration catalog into Rolling's Doctrine ACL tables.
 *
 * The service is intentionally idempotent. It creates only catalog-defined permissions,
 * canonical system roles, role inheritance edges, optional bootstrap assignment, and the
 * role-permission grants needed by the Doctrine-backed administration decision service.
 */
final class DoctrineRollingAdministrationCatalogSyncService implements RollingAdministrationCatalogSyncServiceInterface
{
    private const ROLE_VIEWER = 'administration.viewer';
    private const ROLE_OPERATOR = 'administration.operator';
    private const ROLE_SECURITY_ADMIN = 'administration.security_admin';

    public function __construct(
        private readonly ManagerRegistry $registry,
        private readonly RollingAdministrationPermissionCatalogInterface $permissionCatalog,
    ) {
    }

    /**
     * @param list<string> $bootstrapSubjectIdentifiers
     */
    public function sync(?string $bootstrapSubjectIdentifier = null, array $bootstrapSubjectIdentifiers = []): RollingAdministrationCatalogSyncResult
    {
        $manager = $this->registry->getManagerForClass(RollingPermission::class);
        if (null === $manager) {
            throw new \RuntimeException(sprintf('No Doctrine manager configured for %s.', RollingPermission::class));
        }

        $createdPermissions = 0;
        $updatedPermissions = 0;
        $createdRoles = 0;
        $createdHierarchyEdges = 0;
        $createdRolePermissions = 0;
        $removedRolePermissions = 0;
        $createdAssignments = 0;

        $descriptors = $this->permissionCatalog->descriptors();
        foreach ($descriptors as $descriptor) {
            if (!$descriptor instanceof RollingAdministrationPermissionDescriptor) {
                continue;
            }

            $permission = $manager->getRepository(RollingPermission::class)->findOneBy(['permissionKey' => $descriptor->key()]);
            if (!$permission instanceof RollingPermission) {
                $permission = new RollingPermission($descriptor->key(), 'administration');
                $permission->setDescription($descriptor->label());
                $manager->persist($permission);
                ++$createdPermissions;
                continue;
            }

            $changed = false;
            if ('administration' !== $permission->componentName()) {
                $permission->setComponentName('administration');
                $changed = true;
            }

            if ($descriptor->label() !== (string) $permission->description()) {
                $permission->setDescription($descriptor->label());
                $changed = true;
            }

            if ($changed) {
                ++$updatedPermissions;
            }
        }

        $createdRoles += $this->ensureSystemRole($manager, self::ROLE_VIEWER, 'Administration Viewer');
        $createdRoles += $this->ensureSystemRole($manager, self::ROLE_OPERATOR, 'Administration Operator');
        $createdRoles += $this->ensureSystemRole($manager, self::ROLE_SECURITY_ADMIN, 'Administration Security Admin');

        $createdHierarchyEdges += $this->ensureHierarchy($manager, self::ROLE_OPERATOR, self::ROLE_VIEWER);
        $createdHierarchyEdges += $this->ensureHierarchy($manager, self::ROLE_SECURITY_ADMIN, self::ROLE_OPERATOR);

        $desiredRolePermissions = $this->desiredSystemRolePermissions($descriptors);
        foreach ($desiredRolePermissions as $roleKey => $permissionKeys) {
            foreach ($permissionKeys as $permissionKey) {
                $createdRolePermissions += $this->ensureRolePermission($manager, $roleKey, $permissionKey);
            }
        }

        $removedRolePermissions += $this->removeObsoleteSystemRolePermissions($manager, $desiredRolePermissions);

        $bootstrapSubjects = $this->normalizeBootstrapSubjects($bootstrapSubjectIdentifier, $bootstrapSubjectIdentifiers);
        foreach ($bootstrapSubjects as $subjectIdentifier) {
            $createdAssignments += $this->ensureAssignment($manager, $subjectIdentifier, self::ROLE_SECURITY_ADMIN, 'administering:global');
        }

        $manager->flush();

        return new RollingAdministrationCatalogSyncResult([
            'created_permissions' => $createdPermissions,
            'updated_permissions' => $updatedPermissions,
            'created_roles' => $createdRoles,
            'created_hierarchy_edges' => $createdHierarchyEdges,
            'created_role_permissions' => $createdRolePermissions,
            'removed_role_permissions' => $removedRolePermissions,
            'created_assignments' => $createdAssignments,
            'bootstrap_subject_count' => count($bootstrapSubjects),
            'bootstrap_subjects' => implode(',', $bootstrapSubjects),
        ]);
    }

    /**
     * @param list<RollingAdministrationPermissionDescriptor> $descriptors
     *
     * @return array<string, list<string>>
     */
    private function desiredSystemRolePermissions(array $descriptors): array
    {
        $desired = [
            self::ROLE_VIEWER => [],
            self::ROLE_OPERATOR => [],
            self::ROLE_SECURITY_ADMIN => [],
        ];

        foreach ($descriptors as $descriptor) {
            if (!$descriptor instanceof RollingAdministrationPermissionDescriptor) {
                continue;
            }

            $permissionKey = trim($descriptor->key());
            if ('' === $permissionKey) {
                continue;
            }

            $desired[self::ROLE_SECURITY_ADMIN][$permissionKey] = true;

            if (!$descriptor->sensitive()) {
                $desired[self::ROLE_OPERATOR][$permissionKey] = true;
            }

            if (str_ends_with($permissionKey, '.view')) {
                $desired[self::ROLE_VIEWER][$permissionKey] = true;
            }
        }

        return array_map(
            static function (array $permissionMap): array {
                $permissions = array_keys($permissionMap);
                sort($permissions);

                return $permissions;
            },
            $desired,
        );
    }

    /**
     * @param array<string, list<string>> $desiredRolePermissions
     */
    private function removeObsoleteSystemRolePermissions(ObjectManager $manager, array $desiredRolePermissions): int
    {
        $removed = 0;
        $systemRoles = [self::ROLE_VIEWER, self::ROLE_OPERATOR, self::ROLE_SECURITY_ADMIN];

        foreach ($systemRoles as $roleKey) {
            $desired = array_fill_keys($desiredRolePermissions[$roleKey] ?? [], true);
            $grants = $manager->getRepository(RollingRolePermission::class)->findBy([
                'roleKey' => $roleKey,
                'scopePattern' => 'administering:*',
            ]);

            foreach ($grants as $grant) {
                if (!$grant instanceof RollingRolePermission) {
                    continue;
                }

                $permissionKey = trim($grant->permissionKey());
                if ('allow' !== $grant->effect() || isset($desired[$permissionKey])) {
                    continue;
                }

                $manager->remove($grant);
                ++$removed;
            }
        }

        return $removed;
    }

    /**
     * @param list<string> $bootstrapSubjectIdentifiers
     *
     * @return list<string>
     */
    private function normalizeBootstrapSubjects(?string $primarySubjectIdentifier, array $bootstrapSubjectIdentifiers): array
    {
        $subjects = [];

        foreach (array_merge([(string) $primarySubjectIdentifier], $bootstrapSubjectIdentifiers) as $subjectIdentifier) {
            $subjectIdentifier = trim((string) $subjectIdentifier);
            if ('' === $subjectIdentifier) {
                continue;
            }

            $subjects[$subjectIdentifier] = true;
        }

        return array_keys($subjects);
    }

    private function ensureSystemRole(ObjectManager $manager, string $roleKey, string $label): int
    {
        $role = $manager->getRepository(RollingRole::class)->findOneBy(['roleKey' => $roleKey]);
        if ($role instanceof RollingRole) {
            if (!$role->systemRole() || !$role->enabled() || $role->label() !== $label) {
                $role->setSystemRole(true);
                $role->setEnabled(true);
                $role->setLabel($label);
            }

            return 0;
        }

        $role = new RollingRole($roleKey, $label);
        $role->setSystemRole(true);
        $role->setEnabled(true);
        $manager->persist($role);

        return 1;
    }

    private function ensureHierarchy(ObjectManager $manager, string $parentRoleKey, string $childRoleKey): int
    {
        $edge = $manager->getRepository(RollingRoleHierarchy::class)->findOneBy([
            'parentRoleKey' => $parentRoleKey,
            'childRoleKey' => $childRoleKey,
        ]);

        if ($edge instanceof RollingRoleHierarchy) {
            return 0;
        }

        $manager->persist(new RollingRoleHierarchy($parentRoleKey, $childRoleKey));

        return 1;
    }

    private function ensureRolePermission(ObjectManager $manager, string $roleKey, string $permissionKey): int
    {
        $grant = $manager->getRepository(RollingRolePermission::class)->findOneBy([
            'roleKey' => $roleKey,
            'permissionKey' => $permissionKey,
            'scopePattern' => 'administering:*',
        ]);

        if ($grant instanceof RollingRolePermission) {
            if ('allow' !== $grant->effect()) {
                $grant->setEffect('allow');
            }

            return 0;
        }

        $grant = new RollingRolePermission($roleKey, $permissionKey, 'administering:*');
        $grant->setEffect('allow');
        $manager->persist($grant);

        return 1;
    }

    private function ensureAssignment(ObjectManager $manager, string $subjectIdentifier, string $roleKey, string $scopeKey): int
    {
        $assignment = $manager->getRepository(RollingSubjectRoleAssignment::class)->findOneBy([
            'subjectIdentifier' => $subjectIdentifier,
            'roleKey' => $roleKey,
            'scopeKey' => $scopeKey,
        ]);

        if ($assignment instanceof RollingSubjectRoleAssignment) {
            return 0;
        }

        $manager->persist(new RollingSubjectRoleAssignment($subjectIdentifier, $roleKey, $scopeKey));

        return 1;
    }
}
