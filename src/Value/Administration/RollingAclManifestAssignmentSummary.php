<?php

declare(strict_types=1);

namespace App\Rolling\Value\Administration;

/**
 * Aggregate-only assignment metadata. It intentionally does not expose subject grants.
 */
final class RollingAclManifestAssignmentSummary
{
    public function __construct(
        private readonly int $subjectAssignments,
        private readonly int $resourceRules,
        private readonly int $roleLinks,
    ) {
    }

    public function subjectAssignments(): int
    {
        return $this->subjectAssignments;
    }

    public function resourceRules(): int
    {
        return $this->resourceRules;
    }

    public function roleLinks(): int
    {
        return $this->roleLinks;
    }

    /** @return array{subject_assignments: int, resource_rules: int, role_links: int} */
    public function toArray(): array
    {
        return [
            'subject_assignments' => $this->subjectAssignments,
            'resource_rules' => $this->resourceRules,
            'role_links' => $this->roleLinks,
        ];
    }
}
