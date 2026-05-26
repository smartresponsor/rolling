<?php

declare(strict_types=1);

namespace App\Rolling\ServiceInterface\Administration;

interface RollingAdministrationPermissionDecisionServiceInterface
{
    /** @param array<string, mixed> $context */
    public function isGranted(string $subjectIdentifier, string $permission, string $scope = 'global', array $context = []): bool;
}
