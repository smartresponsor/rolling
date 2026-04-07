<?php

declare(strict_types=1);

namespace App\Policy\Decorator\V2;

use App\Legacy\Policy\Registry\PolicyRegistry;
use App\Policy\V2\DecisionWithObligations;
use App\PolicyInterface\PdpV2Interface;
use App\Entity\Role\Scope;
use App\Entity\Role\PermissionKey;
use App\Entity\Role\SubjectId;

/**
 *
 */

/**
 *
 */
final class RegistryBackedPdp implements PdpV2Interface
{
    /**
     * @param \App\PolicyInterface\PdpV2Interface $inner
     * @param \Policy\Role\Registry\PolicyRegistry $registry
     */
    public function __construct(private readonly PdpV2Interface $inner, private readonly PolicyRegistry $registry)
    {
    }

    /**
     * @param \App\Entity\Role\SubjectId $subject
     * @param \App\Entity\Role\PermissionKey $action
     * @param \App\Entity\Role\Scope $objectScope
     * @param array $context
     * @return \App\Policy\V2\DecisionWithObligations
     */
    public function check(SubjectId $subject, PermissionKey $action, Scope $objectScope, array $context = []): DecisionWithObligations
    {
        $d = $this->inner->check($subject, $action, $objectScope, $context);
        if ($d->isDeny()) return $d;

        $rs = $this->registry->ruleSetFor($subject, $action, $objectScope, $context);
        $extra = $rs->eval($subject, $action, $objectScope, $context);

        if ($extra->isEmpty()) return $d;

        // merge obligations
        foreach ($extra->all() as $o) {
            $d->obligations->add($o);
        }
        return $d;
    }
}
