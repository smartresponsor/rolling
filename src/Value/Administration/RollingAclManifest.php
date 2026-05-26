<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Safe ACL manifest projection for Administering and host diagnostics.
 */
final class RollingAclManifest
{
    /**
     * @param list<RollingAclManifestPermission> $permissions
     * @param list<RollingAclManifestRole>       $roles
     */
    public function __construct(
        private readonly string $version,
        private readonly \DateTimeImmutable $generatedAt,
        private readonly array $permissions,
        private readonly array $roles = [],
        private readonly ?RollingAclManifestAssignmentSummary $assignmentSummary = null,
    ) {
    }

    public function version(): string
    {
        return $this->version;
    }

    public function generatedAt(): \DateTimeImmutable
    {
        return $this->generatedAt;
    }

    /** @return list<RollingAclManifestPermission> */
    public function permissions(): array
    {
        return $this->permissions;
    }

    /** @return list<RollingAclManifestRole> */
    public function roles(): array
    {
        return $this->roles;
    }

    public function assignmentSummary(): ?RollingAclManifestAssignmentSummary
    {
        return $this->assignmentSummary;
    }

    /** @return array{version: string, generated_at: string, permissions: list<array{key: string, label: string, category: string, scopes: list<string>, sensitive: bool}>, roles: list<array{key: string, label: string, inherits: list<string>, system: bool}>, assignment_summary: array{subject_assignments: int, resource_rules: int, role_links: int}|null} */
    public function toArray(): array
    {
        return [
            'version' => $this->version,
            'generated_at' => $this->generatedAt->format(DATE_ATOM),
            'permissions' => array_map(
                static fn (RollingAclManifestPermission $permission): array => $permission->toArray(),
                $this->permissions,
            ),
            'roles' => array_map(
                static fn (RollingAclManifestRole $role): array => $role->toArray(),
                $this->roles,
            ),
            'assignment_summary' => null === $this->assignmentSummary ? null : $this->assignmentSummary->toArray(),
        ];
    }
}
