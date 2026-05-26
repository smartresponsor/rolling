<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Operator-safe report describing how a subject receives Rolling administration access.
 */
final readonly class RollingAdministrationSubjectAccessReport
{
    /**
     * @param list<array<string, mixed>> $assignedRoles
     * @param list<array<string, mixed>> $directRules
     * @param list<array<string, mixed>> $rolePermissions
     * @param list<string>               $effectiveRoles
     * @param list<string>               $grantedPermissions
     * @param list<string>               $deniedPermissions
     * @param list<string>               $cataloguedPermissions
     */
    public function __construct(
        private string $subjectIdentifier,
        private string $scope,
        private array $assignedRoles,
        private array $effectiveRoles,
        private array $directRules,
        private array $rolePermissions,
        private array $grantedPermissions,
        private array $deniedPermissions,
        private array $cataloguedPermissions,
    ) {
    }

    public function subjectIdentifier(): string
    {
        return $this->subjectIdentifier;
    }

    public function scope(): string
    {
        return $this->scope;
    }

    /** @return list<array<string, mixed>> */
    public function assignedRoles(): array
    {
        return $this->assignedRoles;
    }

    /** @return list<string> */
    public function effectiveRoles(): array
    {
        return $this->effectiveRoles;
    }

    /** @return list<array<string, mixed>> */
    public function directRules(): array
    {
        return $this->directRules;
    }

    /** @return list<array<string, mixed>> */
    public function rolePermissions(): array
    {
        return $this->rolePermissions;
    }

    /** @return list<string> */
    public function grantedPermissions(): array
    {
        return $this->grantedPermissions;
    }

    /** @return list<string> */
    public function deniedPermissions(): array
    {
        return $this->deniedPermissions;
    }

    /** @return list<string> */
    public function cataloguedPermissions(): array
    {
        return $this->cataloguedPermissions;
    }

    /** @return array<string, mixed> */
    public function toArray(): array
    {
        return [
            'subject_identifier' => $this->subjectIdentifier,
            'scope' => $this->scope,
            'assigned_roles' => $this->assignedRoles,
            'effective_roles' => $this->effectiveRoles,
            'direct_rules' => $this->directRules,
            'role_permissions' => $this->rolePermissions,
            'granted_permissions' => $this->grantedPermissions,
            'denied_permissions' => $this->deniedPermissions,
            'catalogued_permissions' => $this->cataloguedPermissions,
            'summary' => [
                'assigned_role_count' => count($this->assignedRoles),
                'effective_role_count' => count($this->effectiveRoles),
                'direct_rule_count' => count($this->directRules),
                'role_permission_count' => count($this->rolePermissions),
                'granted_permission_count' => count($this->grantedPermissions),
                'denied_permission_count' => count($this->deniedPermissions),
                'catalogued_permission_count' => count($this->cataloguedPermissions),
            ],
        ];
    }
}
