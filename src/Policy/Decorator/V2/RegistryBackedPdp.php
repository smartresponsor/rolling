<?php

declare(strict_types=1);

namespace App\Rolling\Policy\Decorator\V2;

use App\Rolling\Entity\Role\PermissionKey;
use App\Rolling\Entity\Role\Scope;
use App\Rolling\Entity\Role\SubjectId;
use App\Rolling\Infrastructure\Policy\Registry\PolicyRegistry;
use App\Rolling\Policy\V2\DecisionWithObligations;
use App\Rolling\ServiceInterface\Policy\PdpV2Interface;

final class RegistryBackedPdp implements PdpV2Interface
{
    public function __construct(
        private readonly PdpV2Interface $inner,
        private readonly PolicyRegistry $registry,
    ) {
    }

    /**
     * @param array<string, mixed> $context
     */
    public function check(SubjectId $subject, PermissionKey $action, Scope $objectScope, array $context = []): DecisionWithObligations
    {
        $decision = $this->inner->check($subject, $action, $objectScope, $context);
        if ($decision->isDeny()) {
            return $decision;
        }

        $ruleSet = $this->registry->ruleSetFor($subject, $action, $objectScope, $context);
        $extra = $ruleSet->eval($subject, $action, $objectScope, $context);

        if ([] === $extra->all()) {
            return $decision;
        }

        $merged = $decision->obligations();
        foreach ($extra->all() as $obligation) {
            $merged = $merged->with($obligation);
        }

        return new DecisionWithObligations($decision->isAllow(), $decision->reason(), $merged);
    }
}
