<?php

declare(strict_types=1);

namespace App\Rolling\Service\Administration;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\ServiceInterface\Administration\RollingAdministrationPermissionDecisionServiceInterface;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;

final class PdpRollingAdministrationPermissionDecisionService implements RollingAdministrationPermissionDecisionServiceInterface
{
    public function __construct(private readonly PdpV2Interface $pdp)
    {
    }

    public function isGranted(string $subjectIdentifier, string $permission, string $scope = 'global', array $context = []): bool
    {
        $decision = $this->pdp->check(
            new SubjectId($subjectIdentifier),
            new PermissionKey($permission),
            Scope::fromKey($scope),
            $context + ['administration_scope' => $scope],
        );

        return $decision->isAllow();
    }
}
